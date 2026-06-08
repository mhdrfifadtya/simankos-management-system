export interface Tenant {
  id: number
  name: string
  whatsapp_number: string
  address: string
  room_id: number
  start_date: string
  contract_duration: number
}

export interface Room {
  id: number
  room_number: string
  type: string
  price: number
  facilities: string
  status: 'available' | 'occupied'
}

export interface Payment {
  id: number
  tenant_id: number
  amount: number
  payment_date: string | null
  due_date: string
  status: 'unpaid' | 'paid' | 'overdue' | 'pending'
  proof_image: string | null
  month_label: string
}

export interface Notification {
  id: number
  tenant_id: number
  payment_id: number
  message: string
  sent_at: string
  status: 'sent' | 'failed'
  type: 'H7' | 'H3' | 'H1' | 'overdue' | 'paid'
  is_read: boolean
}

export interface AuthResponse {
  token: string
  tenant: Tenant
  room: Room
}
