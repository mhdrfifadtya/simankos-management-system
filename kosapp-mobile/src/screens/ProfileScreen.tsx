import React from 'react'
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  Alert,
  Linking,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import { SafeAreaView } from 'react-native-safe-area-context'
import { LinearGradient } from 'expo-linear-gradient'
import { useNavigation } from '@react-navigation/native'
import type { StackNavigationProp } from '@react-navigation/stack'
import { COLORS, FONT_SIZE, SPACING, RADIUS } from '../constants/theme'
import SkylineBackground from '../components/SkylineBackground'
import GlassCard from '../components/GlassCard'
import BottomNavBar from '../components/BottomNavBar'
import { useAuthStore } from '../store/authStore'
import { useNotificationStore } from '../store/notificationStore'
import { formatDate } from '../utils/formatDate'
import { formatCurrency } from '../utils/formatCurrency'
import type { RootStackParamList } from '../navigation/AppNavigator'

type Nav = StackNavigationProp<RootStackParamList, 'Profile'>

const OWNER_WHATSAPP = '6281234000000'

function getInitials(name: string): string {
  return name
    .split(' ')
    .slice(0, 2)
    .map((w) => w[0])
    .join('')
    .toUpperCase()
}

export default function ProfileScreen() {
  const navigation = useNavigation<Nav>()
  const { tenant, room, logout } = useAuthStore()
  const { unreadCount } = useNotificationStore()

  if (!tenant || !room) return null

  const contractEnd = new Date(tenant.start_date)
  contractEnd.setMonth(contractEnd.getMonth() + tenant.contract_duration)
  const contractEndStr = contractEnd.toISOString().split('T')[0]

  const handleContactOwner = () => {
    const msg = encodeURIComponent(
      `Halo, saya ${tenant.name} penghuni kamar ${room.room_number}. Ingin menghubungi Anda.`
    )
    const url = `whatsapp://send?phone=${OWNER_WHATSAPP}&text=${msg}`
    Linking.canOpenURL(url).then((can) => {
      if (can) Linking.openURL(url)
      else Alert.alert('Error', 'WhatsApp tidak tersedia di perangkat ini.')
    })
  }

  const handleLogout = () => {
    Alert.alert(
      'Konfirmasi Logout',
      'Apakah Anda yakin ingin keluar dari akun ini?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Logout',
          style: 'destructive',
          onPress: async () => {
            await logout()
            navigation.replace('Login')
          },
        },
      ]
    )
  }

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <SkylineBackground />

      <SafeAreaView style={styles.safe}>
        {/* Header */}
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
            <Text style={styles.backIcon}>←</Text>
          </TouchableOpacity>
          <Text style={styles.title}>Profil Saya</Text>
          <View style={{ width: 36 }} />
        </View>

        <ScrollView
          contentContainerStyle={styles.scroll}
          showsVerticalScrollIndicator={false}
        >
          {/* Avatar section */}
          <View style={styles.avatarSection}>
            <LinearGradient
              colors={[COLORS.indigo, '#4338ca']}
              style={styles.avatarCircle}
            >
              <Text style={styles.initials}>{getInitials(tenant.name)}</Text>
            </LinearGradient>
            <Text style={styles.tenantName}>{tenant.name}</Text>
            <View style={styles.roomBadge}>
              <Text style={styles.roomBadgeText}>Kamar {room.room_number}</Text>
            </View>
          </View>

          {/* Contact info */}
          <GlassCard style={styles.infoCard}>
            <Text style={styles.cardHeader}>Informasi Kontak</Text>
            <InfoRow label="Nomor WhatsApp" value={`+${tenant.whatsapp_number}`} />
            <InfoRow label="Alamat"         value={tenant.address} last />
          </GlassCard>

          {/* Rental info */}
          <GlassCard style={styles.infoCard}>
            <Text style={styles.cardHeader}>Informasi Sewa</Text>
            <InfoRow label="Tanggal Masuk"   value={formatDate(tenant.start_date)} />
            <InfoRow label="Durasi Kontrak"  value={`${tenant.contract_duration} Bulan`} />
            <InfoRow label="Berakhir"         value={formatDate(contractEndStr)} />
            <InfoRow label="Harga/Bulan"     value={formatCurrency(room.price)} highlight />
            <InfoRow label="Fasilitas"        value={room.facilities} />
            <InfoRow label="Status Penyewa"  value="Aktif" success last />
          </GlassCard>

          {/* Actions */}
          <TouchableOpacity
            style={styles.waBtn}
            onPress={handleContactOwner}
            activeOpacity={0.8}
          >
            <Text style={styles.waIcon}>💬</Text>
            <Text style={styles.waBtnText}>Hubungi Pemilik Kos</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.logoutBtn}
            onPress={handleLogout}
            activeOpacity={0.8}
          >
            <Text style={styles.logoutText}>Logout</Text>
          </TouchableOpacity>

          <View style={{ height: 110 }} />
        </ScrollView>
      </SafeAreaView>

      <BottomNavBar
        activeTab="Profile"
        onTabPress={(key) => navigation.navigate(key as any)}
        unreadCount={unreadCount}
      />
    </View>
  )
}

function InfoRow({
  label,
  value,
  highlight,
  success,
  last,
}: {
  label: string
  value: string
  highlight?: boolean
  success?: boolean
  last?: boolean
}) {
  return (
    <View style={[styles.infoRow, last && styles.infoRowLast]}>
      <Text style={styles.infoLabel}>{label}</Text>
      <Text
        style={[
          styles.infoValue,
          highlight && { color: COLORS.accent },
          success  && { color: COLORS.success },
        ]}
      >
        {value}
      </Text>
    </View>
  )
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.background },
  safe:      { flex: 1 },

  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
  },
  backBtn:  { padding: 4 },
  backIcon: { color: COLORS.textPrimary, fontSize: 24 },
  title: {
    flex: 1,
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.xl,
    fontWeight: '700',
    textAlign: 'center',
  },

  scroll: {
    paddingHorizontal: SPACING.lg,
  },

  avatarSection: {
    alignItems: 'center',
    paddingVertical: SPACING.xl,
  },
  avatarCircle: {
    width: 90,
    height: 90,
    borderRadius: 45,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: SPACING.md,
  },
  initials: {
    color: COLORS.white,
    fontSize: 34,
    fontWeight: '700',
  },
  tenantName: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.xl,
    fontWeight: '700',
    marginBottom: SPACING.sm,
    textAlign: 'center',
  },
  roomBadge: {
    backgroundColor: COLORS.accentMuted,
    borderRadius: RADIUS.full,
    paddingHorizontal: SPACING.md,
    paddingVertical: 4,
    borderWidth: 0.5,
    borderColor: COLORS.accent + '55',
  },
  roomBadgeText: {
    color: COLORS.accent,
    fontSize: FONT_SIZE.sm,
    fontWeight: '700',
  },

  infoCard: {
    padding: SPACING.lg,
    marginBottom: SPACING.md,
  },
  cardHeader: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 1,
    marginBottom: SPACING.md,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    paddingVertical: SPACING.sm,
    borderBottomWidth: 0.5,
    borderBottomColor: COLORS.surfaceBorder,
  },
  infoRowLast: {
    borderBottomWidth: 0,
  },
  infoLabel: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.sm,
    flex: 1,
  },
  infoValue: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.sm,
    fontWeight: '500',
    flex: 1.6,
    textAlign: 'right',
  },

  waBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: SPACING.md,
    backgroundColor: 'rgba(37, 211, 102, 0.14)',
    borderRadius: RADIUS.md,
    borderWidth: 0.5,
    borderColor: 'rgba(37,211,102,0.38)',
    padding: SPACING.md,
    gap: SPACING.sm,
  },
  waIcon:   { fontSize: 20 },
  waBtnText: { color: '#25D366', fontSize: FONT_SIZE.lg, fontWeight: '700' },

  logoutBtn: {
    marginTop: SPACING.sm,
    backgroundColor: COLORS.dangerMuted,
    borderRadius: RADIUS.md,
    padding: SPACING.md,
    alignItems: 'center',
    borderWidth: 0.5,
    borderColor: COLORS.danger + '44',
  },
  logoutText: {
    color: COLORS.danger,
    fontSize: FONT_SIZE.lg,
    fontWeight: '700',
  },
})
