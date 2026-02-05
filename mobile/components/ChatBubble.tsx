import { useEffect, useRef } from 'react';
import { Animated, StyleSheet, Text, View } from 'react-native';
import { Timer } from 'lucide-react-native';
import SpotifyIcon from './SpotifyIcon';
import { Message } from '../types/chat';

type ChatBubbleProps = Message & { animate?: boolean };

export default function ChatBubble({ role, content, animate = false }: ChatBubbleProps) {
  const isUser = role === 'user';
  const isTimer = role === 'timer';
  const isSpotify = role === 'spotify';
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
    : isSpotify
      ? styles.spotifyBubble
      : isUser
        ? styles.userBubble
        : styles.assistantBubble;

  const containerAlign = isTimer || isSpotify
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
        {/* Corner accents on assistant bubbles */}
        {!isUser && !isTimer && !isSpotify && (
          <>
            <View style={styles.cornerTL} />
            <View style={styles.cornerBR} />
          </>
        )}

        {/* User bubble glow accent */}
        {isUser && <View style={styles.userAccent} />}

        {isTimer ? (
          <View style={styles.actionContent}>
            <Timer size={14} color="#ff6b35" />
            <Text style={styles.timerText}>{content}</Text>
          </View>
        ) : isSpotify ? (
          <View style={styles.actionContent}>
            <SpotifyIcon size={14} color="#1DB954" />
            <Text style={styles.spotifyText}>{content}</Text>
          </View>
        ) : !isUser ? (
          <View style={styles.assistantInner}>
            <Text style={styles.promptPrefix}>{'>'}</Text>
            <Text style={[styles.text, styles.assistantText]}>{content}</Text>
          </View>
        ) : (
          <Text style={[styles.text, styles.userText]}>{content}</Text>
        )}
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 12,
    paddingVertical: 3,
  },
  userContainer: {
    alignItems: 'flex-end',
  },
  assistantContainer: {
    alignItems: 'flex-start',
  },
  bubble: {
    maxWidth: '82%',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 4,
    position: 'relative',
  },
  userBubble: {
    backgroundColor: '#ff1744',
    borderBottomRightRadius: 0,
    overflow: 'hidden',
  },
  userAccent: {
    position: 'absolute',
    top: 0,
    right: 0,
    width: 20,
    height: 2,
    backgroundColor: 'rgba(255,255,255,0.3)',
  },
  assistantBubble: {
    backgroundColor: 'rgba(17,17,17,0.9)',
    borderBottomLeftRadius: 0,
    borderWidth: 1,
    borderColor: 'rgba(255,23,68,0.12)',
  },
  assistantInner: {
    flexDirection: 'row',
    gap: 8,
  },
  promptPrefix: {
    fontFamily: 'SpaceMono_700Bold',
    fontSize: 14,
    lineHeight: 22,
    color: 'rgba(255,23,68,0.4)',
  },
  cornerTL: {
    position: 'absolute',
    top: -1,
    left: -1,
    width: 8,
    height: 8,
    borderTopWidth: 1,
    borderLeftWidth: 1,
    borderColor: '#ff1744',
  },
  cornerBR: {
    position: 'absolute',
    bottom: -1,
    right: -1,
    width: 8,
    height: 8,
    borderBottomWidth: 1,
    borderRightWidth: 1,
    borderColor: '#ff6b35',
  },
  text: {
    fontSize: 14,
    lineHeight: 22,
    fontFamily: 'SpaceMono_400Regular',
    flexShrink: 1,
  },
  userText: {
    color: '#FFFFFF',
  },
  assistantText: {
    color: 'rgba(255,255,255,0.85)',
  },
  timerContainer: {
    alignItems: 'center',
  },
  timerBubble: {
    backgroundColor: 'rgba(255,107,53,0.08)',
    borderRadius: 2,
    borderWidth: 1,
    borderColor: 'rgba(255,107,53,0.25)',
  },
  actionContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  timerText: {
    color: '#ff6b35',
    fontSize: 12,
    fontFamily: 'SpaceMono_700Bold',
  },
  spotifyBubble: {
    backgroundColor: 'rgba(29,185,84,0.08)',
    borderRadius: 2,
    borderWidth: 1,
    borderColor: 'rgba(29,185,84,0.25)',
  },
  spotifyText: {
    color: '#1DB954',
    fontSize: 12,
    fontFamily: 'SpaceMono_700Bold',
  },
});
