import React from 'react'
import { View, Text, StyleSheet } from 'react-native'
import { COLORS, FONT_SIZE, SPACING, RADIUS } from '../constants/theme'
import { Notification } from '../types'
import { formatDateTime } from '../utils/formatDate'
import GlassCard from './GlassCard'

interface NotificationCardProps {
  notification: Notification
}

const TYPE_CONFIG: Record<Notification['type'], { label: string; color: string }> = {
  H7:      { label: 'H-7',       color: COLORS.accent },
  H3:      { label: 'H-3',       color: COLORS.warning },
  H1:      { label: 'H-1',       color: COLORS.danger },
  overdue: { label: 'Terlambat', color: COLORS.danger },
  paid:    { label: 'Lunas',     color: COLORS.success },
}

export default function NotificationCard({ notification }: NotificationCardProps) {
  const typeConfig = TYPE_CONFIG[notification.type] ?? { label: notification.type ?? 'Info', color: COLORS.textSecondary }

  return (
    <GlassCard
      style={[
        styles.card,
        !notification.is_read && styles.unread,
      ]}
    >
      <View style={styles.header}>
        <View style={styles.waIcon}>
          <Text style={styles.waText}>WA</Text>
        </View>
        <View style={styles.metaRow}>
          <View style={[styles.typeBadge, { backgroundColor: typeConfig.color + '22' }]}>
            <Text style={[styles.typeText, { color: typeConfig.color }]}>{typeConfig.label}</Text>
          </View>
          <Text style={styles.time}>{formatDateTime(notification.sent_at)}</Text>
        </View>
        {!notification.is_read && <View style={styles.unreadDot} />}
      </View>

      <Text style={styles.message} numberOfLines={3}>
        {notification.message}
      </Text>

      <Text style={styles.via}>via WhatsApp</Text>
    </GlassCard>
  )
}

const styles = StyleSheet.create({
  card: {
    padding: SPACING.md,
    marginBottom: SPACING.sm,
  },
  unread: {
    borderLeftWidth: 3,
    borderLeftColor: COLORS.accent,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.sm,
    gap: SPACING.sm,
  },
  waIcon: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#25D366',
    alignItems: 'center',
    justifyContent: 'center',
  },
  waText: {
    color: '#fff',
    fontSize: FONT_SIZE.xs,
    fontWeight: '700',
  },
  metaRow: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    gap: SPACING.sm,
    flexWrap: 'wrap',
  },
  typeBadge: {
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: RADIUS.full,
  },
  typeText: {
    fontSize: FONT_SIZE.xs,
    fontWeight: '700',
  },
  time: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
  },
  unreadDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: COLORS.accent,
  },
  message: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.md,
    lineHeight: 22,
    marginBottom: 6,
  },
  via: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
  },
})
