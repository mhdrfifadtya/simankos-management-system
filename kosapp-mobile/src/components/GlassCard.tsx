import React from 'react'
import { View, ViewStyle, StyleSheet } from 'react-native'
import { COLORS, RADIUS } from '../constants/theme'

interface GlassCardProps {
  children: React.ReactNode
  style?: ViewStyle
  intensity?: number
  accentColor?: string
}

export default function GlassCard({
  children,
  style,
  accentColor,
}: GlassCardProps) {
  return (
    <View
      style={[
        styles.container,
        accentColor ? { borderColor: accentColor, borderWidth: 0.75 } : {},
        style,
      ]}
    >
      <View style={styles.content}>{children}</View>
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    borderRadius: RADIUS.lg,
    overflow: 'hidden',
    borderWidth: 0.5,
    borderColor: COLORS.surfaceBorder,
    backgroundColor: 'rgba(15, 23, 42, 0.82)',
  },
  content: {
    position: 'relative',
  },
})
