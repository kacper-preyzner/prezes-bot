import { useEffect, useRef } from 'react';
import { Animated, StyleSheet, Text, View } from 'react-native';
import { Timer } from 'lucide-react-native';
import { Message } from '../types/chat';

type ChatBubbleProps = Message & { animate?: boolean };

export default function ChatBubble({ role, content, animate = false }: ChatBubbleProps) {
  const isUser = role === 'user';
  const isTimer = role === 'timer';
  const fadeAnim = useRef(new Animated.Value(animate ? 0 : 1)).current;
  const slideAnim = useRef(new Animated.Value(animate ? (isUser ? 40 : -40) : 0)).current;
  const scaleAnim = useRef(new Animated.Value(animate ? 0.85 : 1)).current;

  useEffect(() => {
    if (!animate) return;

    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 300,
        useNativeDriver: true,
      }),
      Animated.spring(slideAnim, {
        toValue: 0,
        tension: 80,
        friction: 12,
        useNativeDriver: true,
      }),
      Animated.spring(scaleAnim, {
        toValue: 1,
        tension: 80,
        friction: 12,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  const bubbleStyle = isTimer
    ? styles.timerBubble
    : isUser
      ? styles.userBubble
      : styles.assistantBubble;

  const containerAlign = isTimer
    ? styles.timerContainer
    : isUser
      ? styles.userContainer
      : styles.assistantContainer;

  return (
    <View style={[styles.container, containerAlign]}>
      <Animated.View
        style={[
          styles.bubble,
          bubbleStyle,
          {
            opacity: fadeAnim,
            transform: [{ translateX: slideAnim }, { scale: scaleAnim }],
          },
        ]}
      >
        {isTimer ? (
          <View style={styles.timerContent}>
            <Timer size={18} color="#FF9500" />
            <Text style={styles.timerText}>{content}</Text>
          </View>
        ) : (
          <Text style={[styles.text, isUser ? styles.userText : styles.assistantText]}>
            {content}
          </Text>
        )}
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 12,
    paddingVertical: 4,
  },
  userContainer: {
    alignItems: 'flex-end',
  },
  assistantContainer: {
    alignItems: 'flex-start',
  },
  bubble: {
    maxWidth: '80%',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 16,
  },
  userBubble: {
    backgroundColor: '#007AFF',
    borderBottomRightRadius: 4,
  },
  assistantBubble: {
    backgroundColor: '#2C2C2E',
    borderBottomLeftRadius: 4,
  },
  text: {
    fontSize: 16,
    lineHeight: 22,
  },
  userText: {
    color: '#FFFFFF',
  },
  assistantText: {
    color: '#FFFFFF',
  },
  timerContainer: {
    alignItems: 'center',
  },
  timerBubble: {
    backgroundColor: 'rgba(255, 149, 0, 0.15)',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: 'rgba(255, 149, 0, 0.3)',
  },
  timerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  timerText: {
    color: '#FF9500',
    fontSize: 14,
    fontWeight: '600',
  },
});
