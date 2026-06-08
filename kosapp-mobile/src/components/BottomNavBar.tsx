import React from 'react'
import { View, TouchableOpacity, Text, StyleSheet, Platform } from 'react-native'
import { COLORS, FONT_SIZE, SPACING } from '../constants/theme'

interface NavItem {
  key: string
  label: string
}

const NAV_ITEMS: NavItem[] = [
  { key: 'Home',         label: 'Beranda'   },
  { key: 'Payment',      label: 'Tagihan'   },
  { key: 'Notification', label: 'Notifikasi'},
  { key: 'Profile',      label: 'Profil'    },
]

const NAV_ICONS: Record<string, string> = {
  Home:         '⌂',
  Payment:      '◈',
  Notification: '◉',
  Profile:      '◎',
}

interface BottomNavBarProps {
  activeTab: string
  onTabPress: (key: string) => void
  unreadCount?: number
}

export default function BottomNavBar({
  activeTab,
  onTabPress,
  unreadCount = 0,
}: BottomNavBarProps) {
  return (
    <View style={styles.container}>
      <View style={styles.navRow}>
        {NAV_ITEMS.map((item) => {
          const isActive = activeTab === item.key
          return (
            <TouchableOpacity
              key={item.key}
              style={styles.navItem}
              onPress={() => onTabPress(item.key)}
              activeOpacity={0.7}
            >
              {isActive && <View style={styles.activeGlow} />}
              <View style={styles.iconWrap}>
                <Text style={[styles.icon, isActive && styles.iconActive]}>
                  {NAV_ICONS[item.key]}
                </Text>
                {item.key === 'Notification' && unreadCount > 0 && (
                  <View style={styles.badge}>
                    <Text style={styles.badgeText}>
                      {unreadCount > 9 ? '9+' : unreadCount}
                    </Text>
                  </View>
                )}
              </View>
              <Text style={[styles.label, isActive && styles.labelActive]}>
                {item.label}
              </Text>
            </TouchableOpacity>
          )
        })}
      </View>
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    height: Platform.OS === 'ios' ? 85 : 72,
    borderTopWidth: 0.5,
    borderTopColor: 'rgba(255,255,255,0.08)',
    overflow: 'hidden',
    backgroundColor: 'rgba(10, 14, 26, 0.96)',
  },
  navRow: {
    flexDirection: 'row',
    height: '100%',
    alignItems: 'center',
    paddingBottom: Platform.OS === 'ios' ? 16 : 0,
    zIndex: 1,
  },
  navItem: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingTop: 10,
    position: 'relative',
  },
  activeGlow: {
    position: 'absolute',
    top: 0,
    width: 36,
    height: 3,
    borderRadius: 2,
    backgroundColor: COLORS.accent,
    opacity: 0.85,
  },
  iconWrap: {
    position: 'relative',
  },
  icon: {
    fontSize: 22,
    color: COLORS.textHint,
  },
  iconActive: {
    color: COLORS.accent,
  },
  label: {
    fontSize: FONT_SIZE.xs,
    color: COLORS.textHint,
    marginTop: 3,
  },
  labelActive: {
    color: COLORS.accent,
    fontWeight: '700',
  },
  badge: {
    position: 'absolute',
    top: -6,
    right: -10,
    backgroundColor: COLORS.warning,
    borderRadius: 8,
    minWidth: 16,
    height: 16,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 3,
  },
  badgeText: {
    color: '#000',
    fontSize: 9,
    fontWeight: '700',
  },
})
