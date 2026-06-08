import React from 'react'
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native'
import { COLORS, FONT_SIZE, SPACING, RADIUS } from '../constants/theme'
import { Payment } from '../types'
import { formatCurrency } from '../utils/formatCurrency'
import { formatShortDate } from '../utils/formatDate'
import StatusBadge from './StatusBadge'
import GlassCard from './GlassCard'

interface PaymentCardProps {
  payment: Payment
  onPress?: () => void
}

function resolveIconColor(payment: Payment): string {
  if (payment.status === 'paid') return COLORS.success
  if (payment.status === 'pending') return COLORS.indigo
  if (payment.status === 'overdue') return COLORS.danger
  const due = new Date(payment.due_date)
  due.setHours(0, 0, 0, 0)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  if (due < today) return COLORS.danger
  return COLORS.warning
}

function resolveIconGlyph(payment: Payment): string {
  if (payment.status === 'paid') return '✓'
  if (payment.status === 'pending') return '⏳'
  const due = new Date(payment.due_date)
  due.setHours(0, 0, 0, 0)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  if (payment.status === 'overdue' || due < today) return '!'
  return '₹'
}

export default function PaymentCard({ payment, onPress }: PaymentCardProps) {
  const iconColor = resolveIconColor(payment)
  const iconGlyph = resolveIconGlyph(payment)

  return (
    <TouchableOpacity onPress={onPress} activeOpacity={0.75} style={styles.wrapper}>
      <GlassCard style={styles.card}>
        <View style={styles.row}>
          <View
            style={[
              styles.iconCircle,
              { backgroundColor: iconColor + '1a', borderColor: iconColor + '44' },
            ]}
          >
            <Text style={[styles.iconText, { color: iconColor }]}>{iconGlyph}</Text>
          </View>

          <View style={styles.info}>
            <Text style={styles.title}>{payment.month_label}</Text>
            <Text style={styles.sub}>
              {payment.status === 'paid' && payment.payment_date
                ? `Dibayar: ${formatShortDate(payment.payment_date)}`
                : payment.status === 'pending'
                  ? 'Menunggu verifikasi admin'
                  : `Jatuh tempo: ${formatShortDate(payment.due_date)}`}
            </Text>
          </View>

          <View style={styles.right}>
            <Text style={styles.amount}>{formatCurrency(payment.amount)}</Text>
            <StatusBadge status={payment.status} dueDate={payment.due_date} small />
          </View>
        </View>
      </GlassCard>
    </TouchableOpacity>
  )
}

const styles = StyleSheet.create({
  wrapper: {
    marginBottom: SPACING.sm,
  },
  card: {
    padding: SPACING.md,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  iconCircle: {
    width: 42,
    height: 42,
    borderRadius: 21,
    borderWidth: 1,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: SPACING.md,
    flexShrink: 0,
  },
  iconText: {
    fontSize: FONT_SIZE.lg,
    fontWeight: '700',
  },
  info: {
    flex: 1,
  },
  title: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.md,
    fontWeight: '600',
  },
  sub: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.sm,
    marginTop: 3,
  },
  right: {
    alignItems: 'flex-end',
    gap: 4,
  },
  amount: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.sm,
    fontWeight: '700',
  },
})
