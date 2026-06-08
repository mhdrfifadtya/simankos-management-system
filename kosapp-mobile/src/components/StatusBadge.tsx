import React from 'react'
import { View, Text, StyleSheet } from 'react-native'
import { COLORS, FONT_SIZE, RADIUS, SPACING } from '../constants/theme'

type StatusType = 'unpaid' | 'paid' | 'overdue' | 'pending'

interface StatusBadgeProps {
  status: StatusType
  dueDate?: string
  small?: boolean
}

function resolveConfig(status: StatusType, dueDate?: string) {
  if (status === 'paid') {
    return { label: 'Lunas', color: COLORS.success, bg: COLORS.successMuted }
  }
  if (status === 'pending') {
    return { label: 'Menunggu Konfirmasi', color: COLORS.indigo, bg: COLORS.indigoMuted }
  }
  if (status === 'overdue') {
    return { label: 'Terlambat', color: COLORS.danger, bg: COLORS.dangerMuted }
  }
  if (dueDate) {
    const due = new Date(dueDate)
    due.setHours(0, 0, 0, 0)
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    if (due < today) {
      return { label: 'Terlambat', color: COLORS.danger, bg: COLORS.dangerMuted }
    }
  }
  return { label: 'Belum Bayar', color: COLORS.warning, bg: COLORS.warningMuted }
}

export default function StatusBadge({ status, dueDate, small = false }: StatusBadgeProps) {
  const cfg = resolveConfig(status, dueDate)
  return (
    <View style={[styles.badge, { backgroundColor: cfg.bg }, small && styles.small]}>
      <View style={[styles.dot, { backgroundColor: cfg.color }]} />
      <Text style={[styles.text, { color: cfg.color }, small && styles.smallText]}>
        {cfg.label}
      </Text>
    </View>
  )
}

const styles = StyleSheet.create({
  badge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.sm,
    paddingVertical: 4,
    borderRadius: RADIUS.full,
  },
  small: {
    paddingHorizontal: 6,
    paddingVertical: 2,
  },
  dot: {
    width: 5,
    height: 5,
    borderRadius: 3,
    marginRight: 4,
  },
  text: {
    fontSize: FONT_SIZE.sm,
    fontWeight: '600',
  },
  smallText: {
    fontSize: FONT_SIZE.xs,
  },
})
