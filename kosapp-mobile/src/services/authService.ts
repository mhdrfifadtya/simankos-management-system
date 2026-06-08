import AsyncStorage from '@react-native-async-storage/async-storage'
import api from './api'
import { AuthResponse, Tenant, Room } from '../types'

interface ApiUser {
  id: number
  name: string
  email: string
  whatsapp_number: string | null
  tenant?: {
    id: number
    full_name: string
    whatsapp_number: string
    address: string
    status: string
    active_rental?: {
      id: number
      start_date: string
      end_date: string | null
      contract_duration: number | null
      monthly_rent: number
      room?: {
        id: number
        room_number: string
        type: string
        price: number
        description: string | null
        facilities: string | null
        status: string
        branch: string | null
      }
    }
  }
}

function mapUserToAuthResponse(token: string, user: ApiUser): AuthResponse {
  const rental = user.tenant?.active_rental
  const room   = rental?.room

  const tenant: Tenant = {
    id:                user.tenant?.id ?? 0,
    name:              user.tenant?.full_name ?? user.name,
    whatsapp_number:   user.tenant?.whatsapp_number ?? user.whatsapp_number ?? '',
    address:           user.tenant?.address ?? '',
    room_id:           room?.id ?? 0,
    start_date:        rental?.start_date ?? new Date().toISOString().split('T')[0],
    contract_duration: rental?.contract_duration ?? 12,
  }

  const roomData: Room = {
    id:          room?.id ?? 0,
    room_number: room?.room_number ?? '-',
    type:        room?.type ?? '-',
    price:       room?.price ?? rental?.monthly_rent ?? 0,
    facilities:  room?.facilities ?? room?.description ?? '-',
    status:      (room?.status as 'available' | 'occupied') ?? 'occupied',
  }

  return { token, tenant, room: roomData }
}

export const authService = {
  async login(email: string, password: string): Promise<AuthResponse> {
    const response = await api.post<{ token: string; user: ApiUser }>('/auth/login', {
      email,
      password,
      device_name: 'kosapp-mobile',
    })
    await AsyncStorage.setItem('auth_token', response.data.token)
    return mapUserToAuthResponse(response.data.token, response.data.user)
  },

  async logout(): Promise<void> {
    try {
      await api.post('/auth/logout')
    } catch {
      // Ignore server errors on logout
    } finally {
      await AsyncStorage.removeItem('auth_token')
    }
  },

  async getStoredToken(): Promise<string | null> {
    return AsyncStorage.getItem('auth_token')
  },
}
