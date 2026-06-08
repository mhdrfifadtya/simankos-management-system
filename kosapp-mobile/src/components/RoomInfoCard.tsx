import React from 'react'
import { View, Text, StyleSheet } from 'react-native'
import { differenceInMonths, parseISO } from 'date-fns'
import { COLORS, FONT_SIZE, SPACING, RADIUS } from '../constants/theme'
import { Tenant, Room } from '../types'
import { formatDate } from '../utils/formatDate'
import { formatCurrency } from '../utils/formatCurrency'
import ProgressBar from './ProgressBar'
import GlassCard from './GlassCard'

interface RoomInfoCardProps {
  tenant: Tenant
  room: Room
}

export default function RoomInfoCard({ tenant, room }: RoomInfoCardProps) {
  const startDate = parseISO(tenant.start_date)
  const monthsElapsed = Math.max(0, differenceInMonths(new Date(), startDate))
  const progress = Math.min(monthsElapsed / tenant.contract_duration, 1)
  const progressPercent = Math.round(progress * 100)
  const elapsed = Math.min(monthsElapsed, tenant.contract_duration)

  return (
    <GlassCard style={styles.card} accentColor="rgba(125, 211, 252, 0.22)">
      {/* Header row */}
      <View style={styles.headerRow}>
        <View style={styles.activeBadge}>
          <Text style={styles.activeBadgeText}>Kamar Aktif</Text>
        </View>
        <View style={styles.statusRow}>
          <View style={styles.greenDot} />
          <Text style={styles.statusText}>Aktif</Text>
        </View>
      </View>

      {/* Room number */}
      <Text style={styles.roomNumber}>{room.room_number}</Text>
      <Text style={styles.roomType}>{room.type} — {room.facilities}</Text>

      <View style={styles.divider} />

      {/* 3-column info */}
      <View style={styles.infoRow}>
        <InfoItem label="Tanggal Masuk" value={formatDate(tenant.start_date, 'd MMM yyyy')} />
        <InfoItem label="Durasi Kontrak" value={`${tenant.contract_duration} Bln`} center />
        <InfoItem label="Harga / Bulan" value={formatCurrency(room.price)} right highlight />
      </View>

      {/* Progress bar */}
      <View style={styles.progressSection}>
        <ProgressBar progress={progress} height={7} />
        <Text style={styles.progressLabel}>
          {progressPercent}% — {elapsed}/{tenant.contract_duration} bulan
        </Text>
      </View>
    </GlassCard>
  )
}

function InfoItem({
  label,
  value,
  highlight,
  center,
  right,
}: {
  label: string
  value: string
  highlight?: boolean
  center?: boolean
  right?: boolean
}) {
  return (
    <View
      style={[
        styles.infoItem,
        center && { alignItems: 'center' },
        right && { alignItems: 'flex-end' },
      ]}
    >
      <Text style={styles.infoLabel}>{label}</Text>
      <Text style={[styles.infoValue, highlight && { color: COLORS.accent }]}>{value}</Text>
    </View>
  )
}

const styles = StyleSheet.create({
  card: {
    padding: SPACING.lg,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.md,
  },
  activeBadge: {
    backgroundColor: COLORS.accentMuted,
    borderRadius: RADIUS.full,
    paddingHorizontal: 10,
    paddingVertical: 3,
    borderWidth: 0.5,
    borderColor: COLORS.accent + '50',
  },
  activeBadgeText: {
    color: COLORS.accent,
    fontSize: FONT_SIZE.xs,
    fontWeight: '700',
  },
  statusRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  greenDot: {
    width: 7,
    height: 7,
    borderRadius: 4,
    backgroundColor: COLORS.success,
    marginRight: 5,
  },
  statusText: {
    color: COLORS.success,
    fontSize: FONT_SIZE.sm,
    fontWeight: '600',
  },
  roomNumber: {
    fontSize: 44,
    fontWeight: '800',
    color: COLORS.white,
    letterSpacing: 3,
  },
  roomType: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.sm,
    marginTop: 4,
  },
  divider: {
    height: 0.5,
    backgroundColor: COLORS.surfaceBorder,
    marginVertical: SPACING.md,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: SPACING.md,
  },
  infoItem: {
    flex: 1,
  },
  infoLabel: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
    marginBottom: 4,
  },
  infoValue: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.sm,
    fontWeight: '600',
  },
  progressSection: {
    marginTop: 2,
  },
  progressLabel: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.xs,
    marginTop: 7,
    textAlign: 'right',
  },
})
