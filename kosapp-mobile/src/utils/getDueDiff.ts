import { differenceInDays, parseISO } from 'date-fns'

type DueDiffLabel = 'H-7' | 'H-3' | 'H-1' | 'overdue' | 'ok'

export interface DueDiff {
  days: number
  label: DueDiffLabel
}

export function getDueDiff(dueDateStr: string): DueDiff {
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  const dueDate = parseISO(dueDateStr)
  dueDate.setHours(0, 0, 0, 0)

  const days = differenceInDays(dueDate, today)

  let label: DueDiffLabel
  if (days < 0) {
    label = 'overdue'
  } else if (days <= 1) {
    label = 'H-1'
  } else if (days <= 3) {
    label = 'H-3'
  } else if (days <= 7) {
    label = 'H-7'
  } else {
    label = 'ok'
  }

  return { days, label }
}
