import React, { useEffect } from 'react'
import {
  View,
  Text,
  SectionList,
  TouchableOpacity,
  StyleSheet,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import { SafeAreaView } from 'react-native-safe-area-context'
import { useNavigation } from '@react-navigation/native'
import { format, parseISO, isToday, isYesterday } from 'date-fns'
import { id } from 'date-fns/locale'
import type { StackNavigationProp } from '@react-navigation/stack'
import { COLORS, FONT_SIZE, SPACING } from '../constants/theme'
import SkylineBackground from '../components/SkylineBackground'
import NotificationCard from '../components/NotificationCard'
import BottomNavBar from '../components/BottomNavBar'
import GlassCard from '../components/GlassCard'
import { useNotificationStore } from '../store/notificationStore'
import type { Notification } from '../types'
import type { RootStackParamList } from '../navigation/AppNavigator'

type Nav = StackNavigationProp<RootStackParamList, 'Notification'>

function dateGroupKey(dateStr: string): string {
  try {
    const d = parseISO(dateStr)
    if (isToday(d))     return 'Hari Ini'
    if (isYesterday(d)) return 'Kemarin'
    return format(d, 'd MMMM yyyy', { locale: id })
  } catch {
    return 'Sebelumnya'
  }
}

interface Section {
  title: string
  data: Notification[]
}

export default function NotificationScreen() {
  const navigation = useNavigation<Nav>()
  const { notifications, unreadCount, fetchNotifications, markAllRead } =
    useNotificationStore()

  useEffect(() => {
    fetchNotifications()
  }, [])

  // Build sections preserving order
  const sectionMap = new Map<string, Notification[]>()
  notifications.forEach((n) => {
    const key = dateGroupKey(n.sent_at)
    if (!sectionMap.has(key)) sectionMap.set(key, [])
    sectionMap.get(key)!.push(n)
  })
  const sections: Section[] = Array.from(sectionMap.entries()).map(([title, data]) => ({
    title,
    data,
  }))

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
          <Text style={styles.title}>Notifikasi</Text>
          <TouchableOpacity onPress={markAllRead} activeOpacity={0.7}>
            <Text style={styles.markAll}>Tandai dibaca</Text>
          </TouchableOpacity>
        </View>

        {sections.length === 0 ? (
          <View style={styles.emptyWrapper}>
            <GlassCard style={styles.emptyCard}>
              <Text style={styles.emptyText}>Belum ada notifikasi</Text>
            </GlassCard>
          </View>
        ) : (
          <SectionList
            sections={sections}
            keyExtractor={(item) => item.id.toString()}
            renderSectionHeader={({ section: { title } }) => (
              <Text style={styles.sectionDate}>{title}</Text>
            )}
            renderItem={({ item }) => <NotificationCard notification={item} />}
            contentContainerStyle={styles.listContent}
            showsVerticalScrollIndicator={false}
            stickySectionHeadersEnabled={false}
          />
        )}
      </SafeAreaView>

      <BottomNavBar
        activeTab="Notification"
        onTabPress={(key) => navigation.navigate(key as any)}
        unreadCount={unreadCount}
      />
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
  backBtn:  { padding: 4, marginRight: SPACING.sm },
  backIcon: { color: COLORS.textPrimary, fontSize: 24 },
  title:    { flex: 1, color: COLORS.textPrimary, fontSize: FONT_SIZE.xl, fontWeight: '700' },
  markAll:  { color: COLORS.accent, fontSize: FONT_SIZE.sm, fontWeight: '500' },

  listContent: {
    paddingHorizontal: SPACING.lg,
    paddingBottom: 120,
  },
  sectionDate: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
    fontWeight: '700',
    textTransform: 'uppercase',
    letterSpacing: 1,
    marginTop: SPACING.md,
    marginBottom: SPACING.sm,
  },

  emptyWrapper: { flex: 1, padding: SPACING.lg },
  emptyCard:    { padding: SPACING.xl, alignItems: 'center' },
  emptyText:    { color: COLORS.textHint, fontSize: FONT_SIZE.md },
})
