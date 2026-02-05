import { useEffect, useRef, useState } from 'react';
import { Animated, View, TextInput, Pressable, StyleSheet, Text } from 'react-native';
import { Mic, SendHorizontal } from 'lucide-react-native';
import {
  ExpoSpeechRecognitionModule,
  useSpeechRecognitionEvent,
} from 'expo-speech-recognition';

type Props = {
  onSend: (text: string) => void;
  disabled: boolean;
};

export default function ChatInput({ onSend, disabled }: Props) {
  const [text, setText] = useState('');
  const [listening, setListening] = useState(false);
  const [focused, setFocused] = useState(false);
  const borderPulse = useRef(new Animated.Value(0)).current;
  const micPulse = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    if (focused) {
      Animated.loop(
        Animated.sequence([
          Animated.timing(borderPulse, { toValue: 1, duration: 1500, useNativeDriver: false }),
          Animated.timing(borderPulse, { toValue: 0, duration: 1500, useNativeDriver: false }),
        ]),
      ).start();
    } else {
      borderPulse.setValue(0);
    }
  }, [focused]);

  useEffect(() => {
    if (listening) {
      Animated.loop(
        Animated.sequence([
          Animated.timing(micPulse, { toValue: 1, duration: 600, useNativeDriver: true }),
          Animated.timing(micPulse, { toValue: 0, duration: 600, useNativeDriver: true }),
        ]),
      ).start();
    } else {
      micPulse.setValue(0);
    }
  }, [listening]);

  useSpeechRecognitionEvent('result', (ev) => {
    const transcript = ev.results[0]?.transcript;
    if (transcript) {
      setText(transcript);
    }
  });

  useSpeechRecognitionEvent('end', () => {
    setListening(false);
  });

  useSpeechRecognitionEvent('error', () => {
    setListening(false);
  });

  const handleSend = () => {
    const trimmed = text.trim();
    if (!trimmed || disabled) return;
    onSend(trimmed);
    setText('');
  };

  const toggleListening = async () => {
    if (listening) {
      ExpoSpeechRecognitionModule.stop();
      return;
    }

    const { granted } = await ExpoSpeechRecognitionModule.requestPermissionsAsync();
    if (!granted) return;

    ExpoSpeechRecognitionModule.start({
      interimResults: false,
      lang: 'pl-PL',
    });
    setListening(true);
  };

  const animatedBorderColor = borderPulse.interpolate({
    inputRange: [0, 1],
    outputRange: ['rgba(255,23,68,0.2)', 'rgba(255,23,68,0.5)'],
  });

  const micScale = micPulse.interpolate({
    inputRange: [0, 1],
    outputRange: [1, 1.15],
  });

  return (
    <View style={styles.wrapper}>
      {/* Corner accents on the input area */}
      <View style={styles.cornerTL} />
      <View style={styles.cornerTR} />

      <View style={styles.container}>
        <Text style={styles.prompt}>$</Text>
        <Animated.View style={[styles.inputWrap, { borderColor: animatedBorderColor }]}>
          <TextInput
            style={styles.input}
            value={text}
            onChangeText={setText}
            placeholder="..."
            placeholderTextColor="#333"
            editable={!disabled}
            multiline
            onFocus={() => setFocused(true)}
            onBlur={() => setFocused(false)}
            onSubmitEditing={handleSend}
          />
        </Animated.View>
        <Animated.View style={[{ transform: [{ scale: listening ? micScale : 1 }] }]}>
          <Pressable
            style={[styles.button, styles.micButton, listening && styles.micActive, disabled && styles.buttonDisabled]}
            onPress={toggleListening}
            disabled={disabled}
          >
            <Mic size={18} color={listening ? '#fff' : '#ff1744'} />
          </Pressable>
        </Animated.View>
        <Pressable
          style={[styles.button, styles.sendButton, (disabled || !text.trim()) && styles.buttonDisabled]}
          onPress={handleSend}
          disabled={disabled || !text.trim()}
        >
          <SendHorizontal size={18} color="#fff" />
        </Pressable>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  wrapper: {
    position: 'relative',
    backgroundColor: '#080808',
    borderTopWidth: 1,
    borderTopColor: 'rgba(255,23,68,0.1)',
    paddingTop: 10,
  },
  cornerTL: {
    position: 'absolute',
    top: 0,
    left: 12,
    width: 10,
    height: 10,
    borderTopWidth: 1,
    borderLeftWidth: 1,
    borderColor: 'rgba(255,23,68,0.3)',
  },
  cornerTR: {
    position: 'absolute',
    top: 0,
    right: 12,
    width: 10,
    height: 10,
    borderTopWidth: 1,
    borderRightWidth: 1,
    borderColor: 'rgba(255,107,53,0.3)',
  },
  container: {
    flexDirection: 'row',
    alignItems: 'flex-end',
    paddingHorizontal: 12,
    paddingVertical: 8,
  },
  prompt: {
    fontFamily: 'SpaceMono_700Bold',
    fontSize: 16,
    color: 'rgba(255,23,68,0.35)',
    marginRight: 8,
    marginBottom: 10,
  },
  inputWrap: {
    flex: 1,
    borderWidth: 1,
    borderRadius: 2,
    backgroundColor: '#0d0d0d',
  },
  input: {
    minHeight: 40,
    maxHeight: 100,
    paddingHorizontal: 14,
    paddingVertical: 10,
    fontSize: 14,
    fontFamily: 'SpaceMono_400Regular',
    color: '#FFFFFF',
  },
  button: {
    marginLeft: 6,
    borderRadius: 2,
    paddingHorizontal: 14,
    paddingVertical: 11,
  },
  micButton: {
    backgroundColor: 'transparent',
    borderWidth: 1,
    borderColor: 'rgba(255,23,68,0.25)',
  },
  micActive: {
    backgroundColor: '#ff1744',
    borderColor: '#ff1744',
  },
  sendButton: {
    backgroundColor: '#ff1744',
  },
  buttonDisabled: {
    opacity: 0.3,
  },
});
