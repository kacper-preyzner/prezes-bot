import { useEffect, useRef } from 'react';
import { Animated, StyleSheet, Text, View } from 'react-native';

export default function TypingIndicator() {
  const cursorOpacity = useRef(new Animated.Value(1)).current;
  const dotsAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Blinking cursor
    Animated.loop(
      Animated.sequence([
        Animated.timing(cursorOpacity, { toValue: 0, duration: 400, useNativeDriver: true }),
        Animated.timing(cursorOpacity, { toValue: 1, duration: 400, useNativeDriver: true }),
      ]),
    ).start();

    // Dots cycling
    Animated.loop(
      Animated.timing(dotsAnim, {
        toValue: 3,
        duration: 1200,
        useNativeDriver: false,
      }),
    ).start();
  }, []);

  return (
    <View style={styles.container}>
      <View style={styles.bubble}>
        {/* Corner accents */}
        <View style={styles.cornerTL} />
        <View style={styles.cornerBR} />

        <Text style={styles.prefix}>{'>'}</Text>
        <Text style={styles.text}> przetwarzanie</Text>
        <Animated.Text style={[styles.cursor, { opacity: cursorOpacity }]}>_</Animated.Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 12,
    paddingVertical: 3,
    alignItems: 'flex-start',
  },
  bubble: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(17,17,17,0.9)',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 4,
    borderBottomLeftRadius: 0,
    borderWidth: 1,
    borderColor: 'rgba(255,23,68,0.12)',
    position: 'relative',
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
  prefix: {
    fontFamily: 'SpaceMono_700Bold',
    fontSize: 14,
    color: 'rgba(255,23,68,0.4)',
  },
  text: {
    fontFamily: 'SpaceMono_400Regular',
    fontSize: 14,
    color: 'rgba(255,255,255,0.35)',
  },
  cursor: {
    fontFamily: 'SpaceMono_700Bold',
    fontSize: 14,
    color: '#ff1744',
  },
});
