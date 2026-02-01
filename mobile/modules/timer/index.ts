import { requireNativeModule } from 'expo-modules-core';

const TimerModule = requireNativeModule('Timer');

export function setTimer(seconds: number, message: string): void {
  TimerModule.setTimer(seconds, message);
}
