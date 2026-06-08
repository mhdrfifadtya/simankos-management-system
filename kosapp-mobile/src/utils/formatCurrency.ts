export function formatCurrency(amount: number): string {
  const formatted = amount.toLocaleString('id-ID')
  return `Rp ${formatted}`
}
