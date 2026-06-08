import React, { useEffect, useState } from 'react'
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  RefreshControl,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import { SafeAreaView } from 'react-native-safe-area-context'
import { useNavigation } from '@react-navigation/native'
import type { StackNavigationProp } from '@react-navigation/stack'
import { COLORS, FONT_SIZE, SPACING } from '../constants/theme'
import SkylineBackground from '../components/SkylineBackground'
import RoomInfoCard from '../components/RoomInfoCard'
import PaymentCard from '../components/PaymentCard'
import NotificationCard from '../components/NotificationCard'
import BottomNavBar from '../components/BottomNavBar'
import GlassCard from '../components/GlassCard'
import { useAuthStore } from '../store/authStore'
import { usePaymentStore } from '../store/paymentStore'
import { useNotificationStore } from '../store/notificationStore'
import { getCurrentDateFormatted } from '../utils/formatDate'
import type { RootStackParamList } from '../navigation/AppNavigator'

type Nav = StackNavigationProp<RootStackParamList, 'Home'>

export default function HomeScreen() {
  const navigation = useNavigation<Nav>()
  const { tenant, room } = useAuthStore()
  const { payments, fetchPayments } = usePaymentStore()
  const { notifications, unreadCount, fetchNotifications } = useNotificationStore()
  const [refreshing, setRefreshing] = useState(false)

  useEffect(() => {
    fetchPayments()
    fetchNotifications()
  }, [])

  const handleRefresh = async () => {
    setRefreshing(true)
    await Promise.all([fetchPayments(), fetchNotifications()])
    setRefreshing(false)
  }

  const latestPayments    = payments.slice(0, 3)
  const latestNotification = notifications[0] ?? null
  const firstName         = tenant?.name.split(' ')[0] ?? 'Penyewa'

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <SkylineBackground />

      <SafeAreaView style={styles.safe}>
        {/* ── Header ── */}
        <View style={styles.header}>
          <View>
            <Text style={styles.appLogo}>
              <Text style={{ color: COLORS.accent }}>Kos</Text>
              <Text style={{ color: COLORS.white }}>App</Text>
            </Text>
            <Text style={styles.greeting}>Halo, {firstName} 👋</Text>
            <Text style={styles.date}>{getCurrentDateFormatted()}</Text>
          </View>
          <TouchableOpacity
            style={styles.bellWrap}
            onPress={() => navigation.navigate('Notification')}
            activeOpacity={0.7}
          >
            <Text style={styles.bellIcon}>🔔</Text>
            {unreadCount > 0 && <View style={styles.bellDot} />}
          </TouchableOpacity>
        </View>

        {/* ── Scroll content ── */}
        <ScrollView
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.scroll}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={handleRefresh}
              tintColor={COLORS.accent}
              colors={[COLORS.accent]}
            />
          }
        >
          {/* Room info */}
          {tenant && room && (
            <View style={styles.section}>
              <RoomInfoCard tenant={tenant} room={room} />
            </View>
          )}

          {/* Payments section */}
          <View style={styles.section}>
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>Tagihan & Pembayaran</Text>
              <TouchableOpacity onPress={() => navigation.navigate('Payment')} activeOpacity={0.7}>
                <Text style={styles.seeAll}>Lihat Semua →</Text>
              </TouchableOpacity>
            </View>

            {latestPayments.length === 0 ? (
              <GlassCard style={styles.emptyCard}>
                <Text style={styles.emptyText}>Belum ada data tagihan</Text>
              </GlassCard>
            ) : (
              latestPayments.map((payment) => (
                <PaymentCard
                  key={payment.id}
                  payment={payment}
                  onPress={() => navigation.navigate('Payment')}
                />
              ))
            )}
          </View>

          {/* Latest notification */}
          {latestNotification && (
            <View style={styles.section}>
              <View style={styles.sectionHeader}>
                <Text style={styles.sectionTitle}>Notifikasi Terbaru</Text>
                <TouchableOpacity onPress={() => navigation.navigate('Notification')} activeOpacity={0.7}>
                  <Text style={styles.seeAll}>Lihat Semua →</Text>
                </TouchableOpacity>
              </View>
              <NotificationCard notification={latestNotification} />
            </View>
          )}

          <View style={{ height: 110 }} />
        </ScrollView>
      </SafeAreaView>

      <BottomNavBar
        activeTab="Home"
        onTabPress={(key) => {
          if (key !== 'Home') navigation.navigate(key as any)
        }}
        unreadCount={unreadCount}
      />
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  safe: { flex: 1 },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    paddingHorizontal: SPACING.lg,
    paddingTop: SPACING.sm,
    paddingBottom: SPACING.md,
  },
  appLogo: {
    fontSize: 26,
    fontWeight: '800',
    letterSpacing: 0.5,
  },
  greeting: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.xl,
    fontWeight: '700',
    marginTop: 6,
  },
  date: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.sm,
    marginTop: 3,
  },
  bellWrap: {
    position: 'relative',
    padding: 6,
    marginTop: 4,
  },
  bellIcon: { fontSize: 28 },
  bellDot: {
    position: 'absolute',
    top: 4,
    right: 4,
    width: 11,
    height: 11,
    borderRadius: 6,
    backgroundColor: COLORS.warning,
    borderWidth: 2,
    borderColor: COLORS.background,
  },
  scroll: {
    paddingHorizontal: SPACING.lg,
  },
  section: {
    marginBottom: SPACING.lg,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  sectionTitle: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.lg,
    fontWeight: '700',
  },
  seeAll: {
    color: COLORS.accent,
    fontSize: FONT_SIZE.sm,
    fontWeight: '500',
  },
  emptyCard: {
    padding: SPACING.xl,
    alignItems: 'center',
  },
  emptyText: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.md,
  },
})
