import React, { useEffect, useRef } from 'react'
import { View, StyleSheet, Animated } from 'react-native'
import { LinearGradient } from 'expo-linear-gradient'
import { COLORS, RADIUS } from '../constants/theme'

interface ProgressBarProps {
  progress: number
  height?: number
  backgroundColor?: string
}

export default function ProgressBar({
  progress,
  height = 8,
  backgroundColor = 'rgba(255,255,255,0.07)',
}: ProgressBarProps) {
  const animValue = useRef(new Animated.Value(0)).current

  useEffect(() => {
    Animated.timing(animValue, {
      toValue: Math.min(Math.max(progress, 0), 1),
      duration: 900,
      useNativeDriver: false,
    }).start()
  }, [progress])

  const widthInterpolated = animValue.interpolate({
    inputRange: [0, 1],
    outputRange: ['0%', '100%'],
  })

  return (
    <View style={[styles.track, { height, backgroundColor }]}>
      <Animated.View style={[styles.fill, { width: widthInterpolated, height }]}>
        <LinearGradient
          colors={['#7dd3fc', '#818cf8']}
          start={{ x: 0, y: 0 }}
          end={{ x: 1, y: 0 }}
          style={StyleSheet.absoluteFill}
        />
      </Animated.View>
    </View>
  )
}

const styles = StyleSheet.create({
  track: {
    borderRadius: RADIUS.full,
    overflow: 'hidden',
    width: '100%',
  },
  fill: {
    borderRadius: RADIUS.full,
    overflow: 'hidden',
  },
})
