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
import * as Speech from 'expo-speech';
import { Volume2, VolumeOff } from 'lucide-react-native';
import ChatBubble from '../components/ChatBubble';
import ChatInput from '../components/ChatInput';
import { sendMessage } from '../lib/api';
import { getAutoRead, setAutoRead as persistAutoRead } from '../lib/storage';
import { Message } from '../types/chat';

export default function ChatScreen() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [loading, setLoading] = useState(false);
  const [autoRead, setAutoRead] = useState(false);
  const flatListRef = useRef<FlatList<Message>>(null);
  const autoReadRef = useRef(autoRead);

  useEffect(() => {
    getAutoRead().then(setAutoRead);
  }, []);

  useEffect(() => {
    autoReadRef.current = autoRead;
  }, [autoRead]);

  const toggleAutoRead = useCallback(() => {
    setAutoRead((prev) => {
      const next = !prev;
      persistAutoRead(next);
      return next;
    });
  }, []);

  const handleSend = useCallback(async (text: string) => {
    const userMessage: Message = { role: 'user', content: text };
    setMessages((prev) => [...prev, userMessage]);
    setLoading(true);

    try {
      const reply = await sendMessage(text);
      const assistantMessage: Message = { role: 'assistant', content: reply };
      setMessages((prev) => [...prev, assistantMessage]);
      if (autoReadRef.current) {
        Speech.speak(reply, { language: 'pl-PL', pitch: 1.1 });
      }
    } catch (error) {
      const errorMessage: Message = {
        role: 'assistant',
        content: 'Failed to get a response. Please try again.',
      };
      setMessages((prev) => [...prev, errorMessage]);
    } finally {
      setLoading(false);
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
          keyExtractor={(_, index) => index.toString()}
          renderItem={({ item }) => <ChatBubble {...item} />}
          contentContainerStyle={styles.list}
          onContentSizeChange={() =>
            flatListRef.current?.scrollToEnd({ animated: true })
          }
        />
        {loading && (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="small" color="#007AFF" />
          </View>
        )}
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
  loadingContainer: {
    paddingVertical: 8,
    alignItems: 'center',
  },
});
