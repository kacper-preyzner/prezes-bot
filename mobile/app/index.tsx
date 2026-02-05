import { useCallback, useEffect, useRef, useState } from 'react';
import {
  Animated as RNAnimated,
  FlatList,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Volume2, VolumeOff } from 'lucide-react-native';
import SpotifyIcon from '../components/SpotifyIcon';
import CyberBackground from '../components/CyberBackground';
import GlitchText from '../components/GlitchText';
import { setTimer } from '../modules/timer';
import ChatBubble from '../components/ChatBubble';
import ChatInput from '../components/ChatInput';
import TypingIndicator from '../components/TypingIndicator';
import { sendMessage, fetchMessages, fetchNewMessages } from '../lib/api';
import { speakText } from '../lib/tts';
import { getAutoRead, setAutoRead as persistAutoRead } from '../lib/storage';
import { connectSpotify, isSpotifyConnected } from '../lib/spotify';
import { Action, Message } from '../types/chat';

function formatDuration(seconds: number): string {
  if (seconds >= 3600) {
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    return m > 0 ? `${h} godz. ${m} min` : `${h} godz.`;
  }
  if (seconds >= 60) {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return s > 0 ? `${m} min ${s} sek` : `${m} min`;
  }
  return `${seconds} sek`;
}

function StatusDot({ active }: { active: boolean }) {
  const pulse = useRef(new RNAnimated.Value(0)).current;

  useEffect(() => {
    if (!active) return;
    RNAnimated.loop(
      RNAnimated.sequence([
        RNAnimated.timing(pulse, { toValue: 1, duration: 1500, useNativeDriver: true }),
        RNAnimated.timing(pulse, { toValue: 0, duration: 1500, useNativeDriver: true }),
      ]),
    ).start();
  }, [active]);

  const opacity = active
    ? pulse.interpolate({ inputRange: [0, 1], outputRange: [0.6, 1] })
    : 0.2;

  return (
    <RNAnimated.View
      style={[
        styles.statusDot,
        { backgroundColor: active ? '#0f0' : '#ff1744', opacity },
      ]}
    />
  );
}

export default function ChatScreen() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [loading, setLoading] = useState(false);
  const [loadingHistory, setLoadingHistory] = useState(false);
  const [autoRead, setAutoRead] = useState(false);
  const [spotifyConnected, setSpotifyConnected] = useState(false);
  const [nextCursor, setNextCursor] = useState<number | null>(null);
  const flatListRef = useRef<FlatList<Message>>(null);
  const autoReadRef = useRef(autoRead);
  const loadingRef = useRef(false);
  const hasLoadedInitial = useRef(false);
  const messagesRef = useRef(messages);
  const animatedIds = useRef(new Set<number>());

  useEffect(() => {
    getAutoRead().then(setAutoRead);
    isSpotifyConnected().then(setSpotifyConnected).catch(() => {});
  }, []);

  useEffect(() => {
    if (!hasLoadedInitial.current) {
      hasLoadedInitial.current = true;
      fetchMessages().then(({ data, next_cursor }) => {
        setMessages(data);
        setNextCursor(next_cursor);
      });
    }
  }, []);

  useEffect(() => {
    autoReadRef.current = autoRead;
  }, [autoRead]);

  useEffect(() => {
    messagesRef.current = messages;
  }, [messages]);

  useEffect(() => {
    const interval = setInterval(async () => {
      if (loadingRef.current) return;

      const current = messagesRef.current;
      const latestServerId = current
        .filter((m) => m.id > 0)
        .reduce((max, m) => Math.max(max, m.id), 0);

      if (latestServerId === 0) return;

      try {
        const { data } = await fetchNewMessages(latestServerId);
        if (data.length > 0) {
          setMessages((prev) => {
            const existingIds = new Set(prev.map((m) => m.id));
            const newMsgs = data.filter((m) => !existingIds.has(m.id));
            if (newMsgs.length === 0) return prev;

            newMsgs.forEach((m) => animatedIds.current.add(m.id));
            return [...newMsgs.reverse(), ...prev];
          });
        }
      } catch {
        // silently ignore polling errors
      }
    }, 2000);

    return () => clearInterval(interval);
  }, []);

  const toggleAutoRead = useCallback(() => {
    setAutoRead((prev) => {
      const next = !prev;
      persistAutoRead(next);
      return next;
    });
  }, []);

  const loadOlderMessages = useCallback(async () => {
    if (loadingHistory || nextCursor === null) return;
    setLoadingHistory(true);
    try {
      const { data, next_cursor } = await fetchMessages(nextCursor);
      setMessages((prev) => [...prev, ...data]);
      setNextCursor(next_cursor);
    } finally {
      setLoadingHistory(false);
    }
  }, [loadingHistory, nextCursor]);

  const handleConnectSpotify = useCallback(async () => {
    await connectSpotify();
    const connected = await isSpotifyConnected();
    setSpotifyConnected(connected);
  }, []);

  const executeActions = useCallback((actions: Action[]): Message[] => {
    const actionMessages: Message[] = [];
    for (const action of actions) {
      if (action.type === 'set_timer') {
        setTimer(action.seconds, action.message);
        const label = formatDuration(action.seconds);
        const id = -Date.now() - Math.random();
        animatedIds.current.add(id);
        const text = action.message
          ? `Minutnik ustawiony na ${label} — ${action.message}`
          : `Minutnik ustawiony na ${label}`;
        actionMessages.push({ id, role: 'timer', content: text });
      } else if (action.type === 'spotify_playing') {
        const id = -Date.now() - Math.random();
        animatedIds.current.add(id);
        actionMessages.push({
          id,
          role: 'spotify',
          content: `${action.artist} — ${action.track}`,
        });
      }
    }
    return actionMessages;
  }, []);

  const handleSend = useCallback(async (text: string) => {
    setLoading(true);
    loadingRef.current = true;

    setMessages((prev) => [{ id: 0, role: 'user', content: text }, ...prev]);

    try {
      const { userMessage, assistantMessage, actions } = await sendMessage(text);
      animatedIds.current.add(assistantMessage.id);
      const actionMsgs = executeActions(actions);
      setMessages((prev) => [
        ...actionMsgs,
        assistantMessage,
        userMessage,
        ...prev.filter((m) => m.id !== 0),
      ]);
      if (autoReadRef.current) {
        speakText(assistantMessage.content);
      }
    } catch {
      setMessages((prev) => [
        { id: -Date.now() - 1, role: 'assistant' as const, content: 'Failed to get a response. Please try again.' },
        { id: -Date.now(), role: 'user' as const, content: text },
        ...prev.filter((m) => m.id !== 0),
      ]);
    } finally {
      setLoading(false);
      loadingRef.current = false;
    }
  }, []);

  return (
    <View style={styles.root}>
      <CyberBackground />

      <KeyboardAvoidingView
        style={styles.container}
        behavior="padding"
        keyboardVerticalOffset={Platform.OS === 'ios' ? 0 : 0}
      >
        {/* Custom cyberpunk header */}
        <SafeAreaView edges={['top']} style={styles.header}>
          <View style={styles.headerLeft}>
            <GlitchText text="PREZES BOT" />
            <View style={styles.statusRow}>
              <StatusDot active={!loading} />
              <Text style={styles.statusText}>
                {loading ? 'PROCESSING' : 'SYS ONLINE'}
              </Text>
            </View>
          </View>
          <View style={styles.headerRight}>
            <Pressable onPress={handleConnectSpotify} style={styles.headerBtn}>
              <SpotifyIcon size={20} color={spotifyConnected ? '#1DB954' : '#333'} />
            </Pressable>
            <Pressable onPress={toggleAutoRead} style={styles.headerBtn}>
              {autoRead ? (
                <Volume2 size={20} color="#ff1744" />
              ) : (
                <VolumeOff size={20} color="#333" />
              )}
            </Pressable>
          </View>
        </SafeAreaView>

        {/* Divider line */}
        <View style={styles.divider}>
          <View style={styles.dividerLine} />
          <View style={styles.dividerDot} />
          <View style={styles.dividerLine} />
        </View>

        {/* Messages */}
        <FlatList
          ref={flatListRef}
          data={messages}
          inverted
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <ChatBubble {...item} animate={animatedIds.current.has(item.id)} />
          )}
          contentContainerStyle={styles.list}
          onEndReached={loadOlderMessages}
          onEndReachedThreshold={0.5}
          ListFooterComponent={
            loadingHistory ? (
              <View style={styles.loadingHistory}>
                <Text style={styles.loadingText}>{'// LOADING HISTORY'}</Text>
              </View>
            ) : null
          }
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Text style={styles.emptyIcon}>{'>'}_</Text>
              <Text style={styles.emptyText}>{'Rozpocznij rozmowę z Prezesem'}</Text>
              <Text style={styles.emptySubtext}>{'Mów, pisz, planuj'}</Text>
            </View>
          }
        />
        {loading && <TypingIndicator />}
        <SafeAreaView edges={['bottom']}>
          <ChatInput onSend={handleSend} disabled={loading} />
        </SafeAreaView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: '#080808',
  },
  container: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
    paddingHorizontal: 16,
    paddingBottom: 12,
    paddingTop: 8,
  },
  headerLeft: {
    gap: 6,
  },
  headerRight: {
    flexDirection: 'row',
    gap: 4,
  },
  headerBtn: {
    padding: 8,
    borderWidth: 1,
    borderColor: 'rgba(255,23,68,0.1)',
    borderRadius: 2,
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  statusDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
  },
  statusText: {
    fontFamily: 'SpaceMono_400Regular',
    fontSize: 9,
    letterSpacing: 2,
    color: 'rgba(255,255,255,0.3)',
    textTransform: 'uppercase',
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
  },
  dividerLine: {
    flex: 1,
    height: StyleSheet.hairlineWidth,
    backgroundColor: 'rgba(255,23,68,0.15)',
  },
  dividerDot: {
    width: 4,
    height: 4,
    borderRadius: 2,
    backgroundColor: 'rgba(255,23,68,0.3)',
    marginHorizontal: 8,
  },
  list: {
    flexGrow: 1,
    justifyContent: 'flex-end',
    paddingVertical: 12,
  },
  loadingHistory: {
    paddingVertical: 12,
    alignItems: 'center',
  },
  loadingText: {
    fontFamily: 'SpaceMono_400Regular',
    fontSize: 10,
    letterSpacing: 1,
    color: 'rgba(255,23,68,0.4)',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 80,
    gap: 8,
  },
  emptyIcon: {
    fontFamily: 'SpaceMono_700Bold',
    fontSize: 32,
    color: 'rgba(255,23,68,0.2)',
    marginBottom: 8,
  },
  emptyText: {
    fontFamily: 'SpaceMono_400Regular',
    fontSize: 14,
    color: 'rgba(255,255,255,0.3)',
  },
  emptySubtext: {
    fontFamily: 'SpaceMono_400Regular',
    fontSize: 11,
    color: 'rgba(255,255,255,0.15)',
    letterSpacing: 2,
    textTransform: 'uppercase',
  },
});
