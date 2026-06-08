import api from './api'
import { Payment } from '../types'

interface ApiInvoice {
  id: number
  invoice_number: string
  period: string
  year: number
  month: number
  rent_amount: number
  extra_charges: number
  total: number
  due_date: string | null
  paid_date: string | null
  status: string
  payments_count?: number
  paid_amount?: number
  proof_of_payment?: string | null
}

export interface SubmitPaymentPayload {
  invoice_id: number
  amount: number
  payment_date: string
  payment_method: string
  notes?: string
  proof_uri: string
  proof_name: string
  proof_type: string
}

function mapInvoiceToPayment(inv: ApiInvoice, tenantId: number): Payment {
  let status: Payment['status'] = 'unpaid'
  if (inv.status === 'paid' || inv.status === 'lunas') {
    status = 'paid'
  } else if (inv.status === 'overdue' || inv.status === 'terlambat') {
    status = 'overdue'
  } else if (inv.status === 'pending' || inv.status === 'menunggu_konfirmasi') {
    status = 'pending'
  }

  return {
    id:            inv.id,
    tenant_id:     tenantId,
    amount:        inv.total ?? inv.rent_amount,
    payment_date:  inv.paid_date,
    due_date:      inv.due_date ?? '',
    status,
    proof_image:   inv.proof_of_payment ?? null,
    month_label:   inv.period,
  }
}

// tenantId diambil dari authStore saat dipanggil
let _tenantId = 0
export function setPaymentTenantId(id: number) { _tenantId = id }

export const paymentService = {
  async getPayments(): Promise<Payment[]> {
    const response = await api.get<{ data: ApiInvoice[] }>('/invoices')
    const items = Array.isArray(response.data)
      ? response.data
      : (response.data as any).data ?? []
    return items.map((inv: ApiInvoice) => mapInvoiceToPayment(inv, _tenantId))
  },

  async getPaymentDetail(id: number): Promise<Payment> {
    const response = await api.get<ApiInvoice>(`/invoices/${id}`)
    return mapInvoiceToPayment(response.data, _tenantId)
  },

  async submitPayment(payload: SubmitPaymentPayload): Promise<{ message: string }> {
    const form = new FormData()
    form.append('invoice_id', String(payload.invoice_id))
    form.append('amount', String(payload.amount))
    form.append('payment_date', payload.payment_date)
    form.append('payment_method', payload.payment_method)
    if (payload.notes) form.append('notes', payload.notes)
    form.append('proof_of_payment', {
      uri: payload.proof_uri,
      name: payload.proof_name,
      type: payload.proof_type,
    } as any)

    const response = await api.post('/payments', form, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    return response.data
  },
}
