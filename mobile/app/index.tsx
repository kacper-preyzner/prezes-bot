import { useCallback, useEffect, useRef, useState } from 'react';
import {
  FlatList,
  KeyboardAvoidingView,
  NativeScrollEvent,
  NativeSyntheticEvent,
  Platform,
  Pressable,
  StyleSheet,
  ActivityIndicator,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Volume2, VolumeOff } from 'lucide-react-native';
import ChatBubble from '../components/ChatBubble';
import ChatInput from '../components/ChatInput';
import TypingIndicator from '../components/TypingIndicator';
import { sendMessage, fetchMessages, fetchNewMessages } from '../lib/api';
import { speakText } from '../lib/tts';
import { getAutoRead, setAutoRead as persistAutoRead } from '../lib/storage';
import { Message } from '../types/chat';

export default function ChatScreen() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [loading, setLoading] = useState(false);
  const [loadingHistory, setLoadingHistory] = useState(false);
  const [autoRead, setAutoRead] = useState(false);
  const [nextCursor, setNextCursor] = useState<number | null>(null);
  const flatListRef = useRef<FlatList<Message>>(null);
  const autoReadRef = useRef(autoRead);
  const loadingRef = useRef(false);
  const hasLoadedInitial = useRef(false);
  const messagesRef = useRef(messages);
  const animatedIds = useRef(new Set<number>());

  useEffect(() => {
    getAutoRead().then(setAutoRead);
  }, []);

  useEffect(() => {
    if (!hasLoadedInitial.current) {
      hasLoadedInitial.current = true;
      fetchMessages().then(({ data, next_cursor }) => {
        setMessages(data.reverse());
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
            return [...prev, ...newMsgs];
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
      setMessages((prev) => [...data.reverse(), ...prev]);
      setNextCursor(next_cursor);
    } finally {
      setLoadingHistory(false);
    }
  }, [loadingHistory, nextCursor]);

  const handleScroll = useCallback(
    (e: NativeSyntheticEvent<NativeScrollEvent>) => {
      if (e.nativeEvent.contentOffset.y <= 100) {
        loadOlderMessages();
      }
    },
    [loadOlderMessages],
  );

  const handleSend = useCallback(async (text: string) => {
    setLoading(true);
    loadingRef.current = true;

    // Optimistic user message (no id yet, will be replaced by server response)
    setMessages((prev) => [...prev, { id: 0, role: 'user', content: text }]);

    try {
      const { userMessage, assistantMessage } = await sendMessage(text);
      animatedIds.current.add(assistantMessage.id);
      setMessages((prev) => [
        ...prev.filter((m) => m.id !== 0),
        userMessage,
        assistantMessage,
      ]);
      if (autoReadRef.current) {
        speakText(assistantMessage.content);
      }
    } catch {
      setMessages((prev) => [
        ...prev.filter((m) => m.id !== 0),
        { id: -Date.now(), role: 'user', content: text },
        { id: -Date.now() - 1, role: 'assistant', content: 'Failed to get a response. Please try again.' },
      ]);
    } finally {
      setLoading(false);
      loadingRef.current = false;
    }
  }, []);

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior="padding"
      keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 80}
    >
        <SafeAreaView edges={['top']} style={styles.headerRow}>
          <Pressable onPress={toggleAutoRead} style={styles.toggleButton}>
            {autoRead ? (
              <Volume2 size={24} color="#007AFF" />
            ) : (
              <VolumeOff size={24} color="#8E8E93" />
            )}
          </Pressable>
        </SafeAreaView>
        <FlatList
          ref={flatListRef}
          data={messages}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <ChatBubble {...item} animate={animatedIds.current.has(item.id)} />
          )}
          contentContainerStyle={styles.list}
          onContentSizeChange={() =>
            flatListRef.current?.scrollToEnd({ animated: true })
          }
          onScroll={handleScroll}
          scrollEventThrottle={400}
          ListHeaderComponent={
            loadingHistory ? (
              <View style={styles.loadingHistory}>
                <ActivityIndicator size="small" color="#007AFF" />
              </View>
            ) : null
          }
        />
        {loading && <TypingIndicator />}
        <SafeAreaView edges={['bottom']}>
          <ChatInput onSend={handleSend} disabled={loading} />
        </SafeAreaView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#1C1C1E',
  },
  headerRow: {
    alignItems: 'flex-end',
    paddingHorizontal: 16,
    paddingBottom: 4,
  },
  toggleButton: {
    padding: 8,
  },
  list: {
    paddingVertical: 12,
  },
  loadingHistory: {
    paddingVertical: 8,
    alignItems: 'center',
  },
});
