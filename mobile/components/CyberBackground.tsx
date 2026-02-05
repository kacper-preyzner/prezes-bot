import { useEffect, useRef } from 'react';
import { Animated, Dimensions, StyleSheet, View } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

const { height: SCREEN_HEIGHT, width: SCREEN_WIDTH } = Dimensions.get('window');

export default function CyberBackground() {
  const scanAnim = useRef(new Animated.Value(0)).current;
  const pulseAnim = useRef(new Animated.Value(0)).current;
  const flicker = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    // Scan line sweeping down
    Animated.loop(
      Animated.timing(scanAnim, {
        toValue: 1,
        duration: 4000,
        useNativeDriver: true,
      }),
    ).start();

    // Ambient pulse
    Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 3000,
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 0,
          duration: 3000,
          useNativeDriver: true,
        }),
      ]),
    ).start();

    // Random flicker
    const flickerLoop = () => {
      const delay = 3000 + Math.random() * 5000;
      setTimeout(() => {
        Animated.sequence([
          Animated.timing(flicker, { toValue: 0.92, duration: 50, useNativeDriver: true }),
          Animated.timing(flicker, { toValue: 1, duration: 50, useNativeDriver: true }),
          Animated.timing(flicker, { toValue: 0.96, duration: 30, useNativeDriver: true }),
          Animated.timing(flicker, { toValue: 1, duration: 30, useNativeDriver: true }),
        ]).start();
        flickerLoop();
      }, delay);
    };
    flickerLoop();
  }, []);

  const scanTranslateY = scanAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [-20, SCREEN_HEIGHT + 20],
  });

  const pulseOpacity = pulseAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [0.03, 0.08],
  });

  return (
    <Animated.View style={[styles.container, { opacity: flicker }]} pointerEvents="none">
      {/* Base grid — horizontal lines */}
      {Array.from({ length: Math.floor(SCREEN_HEIGHT / 40) }).map((_, i) => (
        <View
          key={`h${i}`}
          style={[
            styles.gridLineH,
            { top: i * 40 },
          ]}
        />
      ))}

      {/* Base grid — vertical lines */}
      {Array.from({ length: Math.floor(SCREEN_WIDTH / 40) }).map((_, i) => (
        <View
          key={`v${i}`}
          style={[
            styles.gridLineV,
            { left: i * 40 },
          ]}
        />
      ))}

      {/* Top ambient glow */}
      <Animated.View style={[styles.topGlow, { opacity: pulseOpacity }]}>
        <LinearGradient
          colors={['rgba(255,23,68,0.4)', 'rgba(255,107,53,0.1)', 'transparent']}
          style={StyleSheet.absoluteFill}
          start={{ x: 0.5, y: 0 }}
          end={{ x: 0.5, y: 1 }}
        />
      </Animated.View>

      {/* Bottom horizon glow */}
      <LinearGradient
        colors={['transparent', 'rgba(255,23,68,0.06)']}
        style={styles.bottomGlow}
        start={{ x: 0.5, y: 0 }}
        end={{ x: 0.5, y: 1 }}
      />

      {/* Sweep scan line */}
      <Animated.View
        style={[
          styles.scanLine,
          { transform: [{ translateY: scanTranslateY }] },
        ]}
      >
        <LinearGradient
          colors={['transparent', 'rgba(255,23,68,0.15)', 'rgba(255,23,68,0.3)', 'rgba(255,23,68,0.15)', 'transparent']}
          style={StyleSheet.absoluteFill}
          start={{ x: 0.5, y: 0 }}
          end={{ x: 0.5, y: 1 }}
        />
      </Animated.View>

      {/* Corner brackets */}
      <View style={[styles.corner, styles.cornerTL]} />
      <View style={[styles.corner, styles.cornerTR]} />
      <View style={[styles.corner, styles.cornerBL]} />
      <View style={[styles.corner, styles.cornerBR]} />
    </Animated.View>
  );
}

const styles = StyleSheet.create({
  container: {
    ...StyleSheet.absoluteFillObject,
    overflow: 'hidden',
  },
  gridLineH: {
    position: 'absolute',
    left: 0,
    right: 0,
    height: StyleSheet.hairlineWidth,
    backgroundColor: 'rgba(255,23,68,0.04)',
  },
  gridLineV: {
    position: 'absolute',
    top: 0,
    bottom: 0,
    width: StyleSheet.hairlineWidth,
    backgroundColor: 'rgba(255,23,68,0.04)',
  },
  topGlow: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    height: SCREEN_HEIGHT * 0.35,
  },
  bottomGlow: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    height: SCREEN_HEIGHT * 0.3,
  },
  scanLine: {
    position: 'absolute',
    left: 0,
    right: 0,
    height: 12,
  },
  corner: {
    position: 'absolute',
    width: 24,
    height: 24,
  },
  cornerTL: {
    top: 8,
    left: 8,
    borderTopWidth: 1,
    borderLeftWidth: 1,
    borderColor: 'rgba(255,23,68,0.25)',
  },
  cornerTR: {
    top: 8,
    right: 8,
    borderTopWidth: 1,
    borderRightWidth: 1,
    borderColor: 'rgba(255,107,53,0.25)',
  },
  cornerBL: {
    bottom: 8,
    left: 8,
    borderBottomWidth: 1,
    borderLeftWidth: 1,
    borderColor: 'rgba(255,107,53,0.25)',
  },
  cornerBR: {
    bottom: 8,
    right: 8,
    borderBottomWidth: 1,
    borderRightWidth: 1,
    borderColor: 'rgba(255,23,68,0.25)',
  },
});
