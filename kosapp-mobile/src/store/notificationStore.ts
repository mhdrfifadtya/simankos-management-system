import { create } from 'zustand'
import { Notification } from '../types'
import { notificationService } from '../services/notificationService'

const mockNotifications: Notification[] = [
  {
    id: 1, tenant_id: 1, payment_id: 1,
    message: 'Tagihan sewa Juni 2026 sebesar Rp 750.000 jatuh tempo 10 hari lagi.',
    sent_at: '2026-05-22T08:00:00', status: 'sent', type: 'H7', is_read: false,
  },
  {
    id: 2, tenant_id: 1, payment_id: 2,
    message: 'Pembayaran sewa Mei 2026 telah dikonfirmasi lunas. Terima kasih!',
    sent_at: '2026-04-30T10:22:00', status: 'sent', type: 'paid', is_read: true,
  },
  {
    id: 3, tenant_id: 1, payment_id: 1,
    message: 'Pengingat: Tagihan sewa Juni 2026 jatuh tempo 3 hari lagi.',
    sent_at: '2026-05-29T09:00:00', status: 'sent', type: 'H3', is_read: false,
  },
]

interface NotificationState {
  notifications: Notification[]
  unreadCount: number
  isLoading: boolean
  fetchNotifications: () => Promise<void>
  markRead: (id: number) => Promise<void>
  markAllRead: () => Promise<void>
}

export const useNotificationStore = create<NotificationState>((set, get) => ({
  notifications: [],
  unreadCount:   0,
  isLoading:     false,

  fetchNotifications: async () => {
    set({ isLoading: true })
    try {
      const { notifications, unreadCount } = await notificationService.getNotifications()
      set({ notifications, unreadCount, isLoading: false })
    } catch {
      const unreadCount = mockNotifications.filter((n) => !n.is_read).length
      set({ notifications: mockNotifications, unreadCount, isLoading: false })
    }
  },

  markRead: async (id: number) => {
    try {
      await notificationService.markRead(id)
    } catch {
      // Update lokal walaupun API gagal
    }
    const notifications = get().notifications.map((n) =>
      n.id === id ? { ...n, is_read: true } : n
    )
    set({ notifications, unreadCount: notifications.filter((n) => !n.is_read).length })
  },

  markAllRead: async () => {
    try {
      await notificationService.markAllRead()
    } catch {
      // Update lokal walaupun API gagal
    }
    const notifications = get().notifications.map((n) => ({ ...n, is_read: true }))
    set({ notifications, unreadCount: 0 })
  },
}))
