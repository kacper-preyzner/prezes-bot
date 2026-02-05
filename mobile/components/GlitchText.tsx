import { useEffect, useRef } from 'react';
import { Animated, StyleSheet, Text, View } from 'react-native';

type Props = {
  text: string;
  style?: object;
};

export default function GlitchText({ text, style }: Props) {
  const redX = useRef(new Animated.Value(0)).current;
  const redY = useRef(new Animated.Value(0)).current;
  const orangeX = useRef(new Animated.Value(0)).current;
  const orangeY = useRef(new Animated.Value(0)).current;
  const clipOpacity = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    const glitch = () => {
      const delay = 2500 + Math.random() * 4000;
      setTimeout(() => {
        // Burst of glitch
        Animated.sequence([
          Animated.parallel([
            Animated.timing(redX, { toValue: -2.5, duration: 40, useNativeDriver: true }),
            Animated.timing(redY, { toValue: 1, duration: 40, useNativeDriver: true }),
            Animated.timing(orangeX, { toValue: 2.5, duration: 40, useNativeDriver: true }),
            Animated.timing(orangeY, { toValue: -1, duration: 40, useNativeDriver: true }),
            Animated.timing(clipOpacity, { toValue: 0.7, duration: 40, useNativeDriver: true }),
          ]),
          Animated.parallel([
            Animated.timing(redX, { toValue: 1.5, duration: 60, useNativeDriver: true }),
            Animated.timing(redY, { toValue: -0.5, duration: 60, useNativeDriver: true }),
            Animated.timing(orangeX, { toValue: -1.5, duration: 60, useNativeDriver: true }),
            Animated.timing(orangeY, { toValue: 0.5, duration: 60, useNativeDriver: true }),
          ]),
          Animated.parallel([
            Animated.timing(redX, { toValue: -1, duration: 50, useNativeDriver: true }),
            Animated.timing(orangeX, { toValue: 1, duration: 50, useNativeDriver: true }),
          ]),
          Animated.parallel([
            Animated.timing(redX, { toValue: 0, duration: 30, useNativeDriver: true }),
            Animated.timing(redY, { toValue: 0, duration: 30, useNativeDriver: true }),
            Animated.timing(orangeX, { toValue: 0, duration: 30, useNativeDriver: true }),
            Animated.timing(orangeY, { toValue: 0, duration: 30, useNativeDriver: true }),
            Animated.timing(clipOpacity, { toValue: 0, duration: 30, useNativeDriver: true }),
          ]),
        ]).start();
        glitch();
      }, delay);
    };
    glitch();
  }, []);

  return (
    <View style={styles.container}>
      {/* Red offset layer */}
      <Animated.Text
        style={[
          styles.text,
          styles.redLayer,
          style,
          {
            opacity: clipOpacity,
            transform: [{ translateX: redX }, { translateY: redY }],
          },
        ]}
      >
        {text}
      </Animated.Text>

      {/* Orange offset layer */}
      <Animated.Text
        style={[
          styles.text,
          styles.orangeLayer,
          style,
          {
            opacity: clipOpacity,
            transform: [{ translateX: orangeX }, { translateY: orangeY }],
          },
        ]}
      >
        {text}
      </Animated.Text>

      {/* Main text layer */}
      <Text style={[styles.text, styles.mainLayer, style]}>{text}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    position: 'relative',
  },
  text: {
    fontFamily: 'SpaceMono_700Bold',
    fontSize: 18,
    letterSpacing: 3,
    textTransform: 'uppercase',
  },
  mainLayer: {
    color: '#ffffff',
  },
  redLayer: {
    position: 'absolute',
    color: '#ff1744',
  },
  orangeLayer: {
    position: 'absolute',
    color: '#ff6b35',
  },
});
