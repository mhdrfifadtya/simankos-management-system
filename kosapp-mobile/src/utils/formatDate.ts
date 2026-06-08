import { format, parseISO } from 'date-fns'
import { id } from 'date-fns/locale'

export function formatDate(dateStr: string, fmt = 'd MMMM yyyy'): string {
  try {
    const date = parseISO(dateStr)
    return format(date, fmt, { locale: id })
  } catch {
    return dateStr
  }
}

export function formatShortDate(dateStr: string): string {
  return formatDate(dateStr, 'd MMM yyyy')
}

export function formatDateTime(dateStr: string): string {
  return formatDate(dateStr, 'd MMM yyyy, HH:mm')
}

export function getCurrentDateFormatted(): string {
  return format(new Date(), 'EEEE, d MMMM yyyy', { locale: id })
}
