import { useCallback, useEffect, useRef, useState } from 'react';
import {
  FlatList,
  KeyboardAvoidingView,
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
        // API returns newest-first, which is what inverted FlatList expects
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
            // Prepend new messages (newest first for inverted list)
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
      // Append older messages at the end (bottom of inverted list = older)
      setMessages((prev) => [...prev, ...data]);
      setNextCursor(next_cursor);
    } finally {
      setLoadingHistory(false);
    }
  }, [loadingHistory, nextCursor]);

  const handleSend = useCallback(async (text: string) => {
    setLoading(true);
    loadingRef.current = true;

    // Optimistic user message at the top (newest) of inverted list
    setMessages((prev) => [{ id: 0, role: 'user', content: text }, ...prev]);

    try {
      const { userMessage, assistantMessage } = await sendMessage(text);
      animatedIds.current.add(assistantMessage.id);
      setMessages((prev) => [
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
    flexGrow: 1,
    justifyContent: 'flex-end',
    paddingVertical: 12,
  },
  loadingHistory: {
    paddingVertical: 8,
    alignItems: 'center',
  },
});
