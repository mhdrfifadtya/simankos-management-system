import { create } from 'zustand'
import { Payment } from '../types'
import { paymentService, SubmitPaymentPayload } from '../services/paymentService'

const mockPayments: Payment[] = [
  {
    id: 1,
    tenant_id: 1,
    month_label: 'Juni 2026',
    due_date: '2026-06-01',
    status: 'unpaid',
    amount: 750000,
    payment_date: null,
    proof_image: null,
  },
  {
    id: 2,
    tenant_id: 1,
    month_label: 'Mei 2026',
    due_date: '2026-05-01',
    status: 'paid',
    amount: 750000,
    payment_date: '2026-04-30',
    proof_image: null,
  },
  {
    id: 3,
    tenant_id: 1,
    month_label: 'April 2026',
    due_date: '2026-04-01',
    status: 'paid',
    amount: 750000,
    payment_date: '2026-03-28',
    proof_image: null,
  },
  {
    id: 4,
    tenant_id: 1,
    month_label: 'Maret 2026',
    due_date: '2026-03-01',
    status: 'paid',
    amount: 750000,
    payment_date: '2026-02-27',
    proof_image: null,
  },
  {
    id: 5,
    tenant_id: 1,
    month_label: 'Februari 2026',
    due_date: '2026-02-01',
    status: 'paid',
    amount: 750000,
    payment_date: '2026-01-31',
    proof_image: null,
  },
]

interface PaymentState {
  payments: Payment[]
  selectedPayment: Payment | null
  isLoading: boolean
  isSubmitting: boolean
  fetchPayments: () => Promise<void>
  setSelectedPayment: (payment: Payment | null) => void
  submitPayment: (payload: SubmitPaymentPayload) => Promise<string>
}

export const usePaymentStore = create<PaymentState>((set) => ({
  payments: [],
  selectedPayment: null,
  isLoading: false,
  isSubmitting: false,

  fetchPayments: async () => {
    set({ isLoading: true })
    try {
      const data = await paymentService.getPayments()
      set({ payments: data, isLoading: false })
    } catch {
      set({ payments: mockPayments, isLoading: false })
    }
  },

  setSelectedPayment: (payment) => set({ selectedPayment: payment }),

  submitPayment: async (payload) => {
    set({ isSubmitting: true })
    try {
      const result = await paymentService.submitPayment(payload)
      // Refresh list after successful submission
      const data = await paymentService.getPayments()
      set({ payments: data, isSubmitting: false })
      return result.message
    } catch (err: any) {
      set({ isSubmitting: false })
      throw err
    }
  },
}))
