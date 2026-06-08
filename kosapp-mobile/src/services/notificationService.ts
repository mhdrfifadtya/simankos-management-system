import api from './api'
import { Notification } from '../types'

interface ApiNotificationsResponse {
  data: Notification[]
  pagination: {
    total: number
    current_page: number
    last_page: number
  }
  unread_count: number
}

export const notificationService = {
  async getNotifications(): Promise<{ notifications: Notification[]; unreadCount: number }> {
    const response = await api.get<ApiNotificationsResponse>('/notifications')
    return {
      notifications: response.data.data ?? [],
      unreadCount:   response.data.unread_count ?? 0,
    }
  },

  async markRead(id: number): Promise<void> {
    await api.patch(`/notifications/${id}/read`)
  },

  async markAllRead(): Promise<void> {
    await api.patch('/notifications/read-all')
  },
}
