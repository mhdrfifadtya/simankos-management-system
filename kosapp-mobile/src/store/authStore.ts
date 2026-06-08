import { create } from 'zustand'
import AsyncStorage from '@react-native-async-storage/async-storage'
import { Tenant, Room } from '../types'
import { authService } from '../services/authService'
import { setPaymentTenantId } from '../services/paymentService'

const mockTenant: Tenant = {
  id:                1,
  name:              'Muhammad Rafif Aditya',
  whatsapp_number:   '628123456789',
  address:           'Jl. Mawar No. 5, Yogyakarta',
  room_id:           3,
  start_date:        '2026-01-01',
  contract_duration: 12,
}

const mockRoom: Room = {
  id:          3,
  room_number: '205',
  type:        'Standard',
  price:       750000,
  facilities:  'AC, WiFi, Kamar Mandi Dalam',
  status:      'occupied',
}

interface AuthState {
  tenant: Tenant | null
  room: Room | null
  token: string | null
  isLoading: boolean
  error: string | null
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
}

export const useAuthStore = create<AuthState>((set) => ({
  tenant:    null,
  room:      null,
  token:     null,
  isLoading: false,
  error:     null,

  login: async (email: string, password: string) => {
    set({ isLoading: true, error: null })
    try {
      const data = await authService.login(email, password)
      setPaymentTenantId(data.tenant.id)
      set({
        tenant:    data.tenant,
        room:      data.room,
        token:     data.token,
        isLoading: false,
        error:     null,
      })
    } catch {
      // Dev fallback — gunakan email demo saat backend tidak tersedia
      if (email === 'penyewa@kos.com' && password === 'password') {
        await AsyncStorage.setItem('auth_token', 'mock_jwt_token_dev')
        setPaymentTenantId(mockTenant.id)
        set({
          tenant:    mockTenant,
          room:      mockRoom,
          token:     'mock_jwt_token_dev',
          isLoading: false,
          error:     null,
        })
      } else {
        set({
          error:     'Email atau kata sandi salah.',
          isLoading: false,
        })
        throw new Error('Login gagal')
      }
    }
  },

  logout: async () => {
    set({ isLoading: true })
    try {
      await authService.logout()
    } finally {
      setPaymentTenantId(0)
      set({ tenant: null, room: null, token: null, isLoading: false, error: null })
    }
  },
}))
