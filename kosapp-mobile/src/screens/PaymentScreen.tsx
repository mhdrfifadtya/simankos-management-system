import React, { useEffect, useState } from 'react'
import {
  View,
  Text,
  FlatList,
  TouchableOpacity,
  StyleSheet,
  Modal,
  ScrollView,
  RefreshControl,
  Alert,
  ActivityIndicator,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import { SafeAreaView } from 'react-native-safe-area-context'
import { useNavigation } from '@react-navigation/native'
import type { StackNavigationProp } from '@react-navigation/stack'
import * as ImagePicker from 'expo-image-picker'
import * as DocumentPicker from 'expo-document-picker'
import { COLORS, FONT_SIZE, SPACING, RADIUS } from '../constants/theme'
import SkylineBackground from '../components/SkylineBackground'
import PaymentCard from '../components/PaymentCard'
import StatusBadge from '../components/StatusBadge'
import BottomNavBar from '../components/BottomNavBar'
import GlassCard from '../components/GlassCard'
import { usePaymentStore } from '../store/paymentStore'
import { useNotificationStore } from '../store/notificationStore'
import { formatCurrency } from '../utils/formatCurrency'
import { formatDate, formatShortDate } from '../utils/formatDate'
import type { Payment } from '../types'
import type { RootStackParamList } from '../navigation/AppNavigator'

type Nav = StackNavigationProp<RootStackParamList, 'Payment'>
type FilterKey = 'all' | 'unpaid' | 'paid' | 'overdue' | 'pending'

const FILTERS: { key: FilterKey; label: string }[] = [
  { key: 'all',     label: 'Semua'       },
  { key: 'unpaid',  label: 'Belum Bayar' },
  { key: 'pending', label: 'Menunggu'    },
  { key: 'paid',    label: 'Lunas'       },
  { key: 'overdue', label: 'Terlambat'   },
]

function isEffectivelyOverdue(p: Payment): boolean {
  if (p.status === 'overdue') return true
  if (p.status === 'unpaid') {
    const due = new Date(p.due_date)
    due.setHours(0, 0, 0, 0)
    return due < new Date(new Date().setHours(0, 0, 0, 0))
  }
  return false
}

interface ProofFile {
  uri: string
  name: string
  type: string
}

export default function PaymentScreen() {
  const navigation = useNavigation<Nav>()
  const { payments, fetchPayments, selectedPayment, setSelectedPayment, isSubmitting, submitPayment } = usePaymentStore()
  const { unreadCount } = useNotificationStore()
  const [filter, setFilter]         = useState<FilterKey>('all')
  const [refreshing, setRefreshing] = useState(false)
  const [modalVisible, setModal]    = useState(false)
  const [proofFile, setProofFile]   = useState<ProofFile | null>(null)
  const [paymentMethod, setPaymentMethod] = useState('Transfer Bank')

  useEffect(() => { fetchPayments() }, [])

  const handleRefresh = async () => {
    setRefreshing(true)
    await fetchPayments()
    setRefreshing(false)
  }

  const filtered = payments.filter((p) => {
    if (filter === 'all')     return true
    if (filter === 'overdue') return isEffectivelyOverdue(p)
    if (filter === 'unpaid')  return p.status === 'unpaid' && !isEffectivelyOverdue(p)
    if (filter === 'pending') return p.status === 'pending'
    return p.status === filter
  })

  const openDetail = (p: Payment) => {
    setSelectedPayment(p)
    setProofFile(null)
    setModal(true)
  }

  const closeModal = () => {
    setModal(false)
    setSelectedPayment(null)
    setProofFile(null)
  }

  const pickFromGallery = async () => {
    const perm = await ImagePicker.requestMediaLibraryPermissionsAsync()
    if (!perm.granted) {
      Alert.alert('Izin Diperlukan', 'Izinkan akses galeri untuk memilih bukti pembayaran.')
      return
    }
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      quality: 0.8,
      allowsEditing: false,
    })
    if (!result.canceled && result.assets[0]) {
      const asset = result.assets[0]
      const ext = asset.uri.split('.').pop() ?? 'jpg'
      setProofFile({ uri: asset.uri, name: `bukti_${Date.now()}.${ext}`, type: `image/${ext}` })
    }
  }

  const pickDocument = async () => {
    const result = await DocumentPicker.getDocumentAsync({
      type: ['image/*', 'application/pdf'],
      copyToCacheDirectory: true,
    })
    if (!result.canceled && result.assets[0]) {
      const asset = result.assets[0]
      setProofFile({
        uri: asset.uri,
        name: asset.name,
        type: asset.mimeType ?? 'application/octet-stream',
      })
    }
  }

  const handlePickProof = () => {
    Alert.alert('Pilih Bukti Pembayaran', '', [
      { text: 'Foto dari Galeri', onPress: pickFromGallery },
      { text: 'File / PDF', onPress: pickDocument },
      { text: 'Batal', style: 'cancel' },
    ])
  }

  const handleSubmitPayment = async () => {
    if (!selectedPayment) return
    if (!proofFile) {
      Alert.alert('Bukti Diperlukan', 'Pilih foto atau file bukti transfer terlebih dahulu.')
      return
    }
    Alert.alert(
      'Konfirmasi Pembayaran',
      `Kirim bukti pembayaran untuk ${selectedPayment.month_label}?`,
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Kirim',
          onPress: async () => {
            try {
              const today = new Date().toISOString().split('T')[0]
              const msg = await submitPayment({
                invoice_id:     selectedPayment.id,
                amount:         selectedPayment.amount,
                payment_date:   today,
                payment_method: paymentMethod,
                proof_uri:      proofFile.uri,
                proof_name:     proofFile.name,
                proof_type:     proofFile.type,
              })
              closeModal()
              Alert.alert('Berhasil', msg)
            } catch (err: any) {
              const msg = err?.response?.data?.message ?? 'Gagal mengirim pembayaran. Coba lagi.'
              Alert.alert('Gagal', msg)
            }
          },
        },
      ]
    )
  }

  return (
    <View style={styles.container}>
      <StatusBar style="light" />
      <SkylineBackground />

      <SafeAreaView style={styles.safe}>
        {/* Header */}
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
            <Text style={styles.backIcon}>←</Text>
          </TouchableOpacity>
          <Text style={styles.title}>Tagihan Sewa</Text>
          <View style={{ width: 36 }} />
        </View>

        {/* Filter tabs */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          style={styles.filterScroll}
          contentContainerStyle={styles.filterRow}
        >
          {FILTERS.map((f) => (
            <TouchableOpacity
              key={f.key}
              style={[styles.tab, filter === f.key && styles.tabActive]}
              onPress={() => setFilter(f.key)}
              activeOpacity={0.75}
            >
              <Text style={[styles.tabText, filter === f.key && styles.tabTextActive]}>
                {f.label}
              </Text>
            </TouchableOpacity>
          ))}
        </ScrollView>

        {/* List */}
        <FlatList
          data={filtered}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => (
            <PaymentCard payment={item} onPress={() => openDetail(item)} />
          )}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={handleRefresh}
              tintColor={COLORS.accent}
              colors={[COLORS.accent]}
            />
          }
          ListEmptyComponent={
            <GlassCard style={styles.emptyCard}>
              <Text style={styles.emptyText}>Tidak ada tagihan ditemukan</Text>
            </GlassCard>
          }
        />
      </SafeAreaView>

      {/* Detail Modal */}
      <Modal
        visible={modalVisible}
        animationType="slide"
        transparent
        onRequestClose={closeModal}
      >
        <View style={styles.modalOverlay}>
          <TouchableOpacity style={StyleSheet.absoluteFillObject} onPress={closeModal} activeOpacity={1} />
          {selectedPayment && (
            <GlassCard style={styles.sheet}>
              <View style={styles.sheetHandle} />
              <ScrollView showsVerticalScrollIndicator={false}>
                <Text style={styles.sheetTitle}>Detail Tagihan</Text>
                <Text style={styles.sheetMonth}>{selectedPayment.month_label}</Text>

                <View style={styles.divider} />

                <ModalRow label="Jumlah Tagihan" value={formatCurrency(selectedPayment.amount)} highlight />
                <ModalRow label="Jatuh Tempo"    value={formatDate(selectedPayment.due_date)} />
                {selectedPayment.payment_date && (
                  <ModalRow label="Tanggal Bayar" value={formatDate(selectedPayment.payment_date)} />
                )}

                <View style={styles.statusModalRow}>
                  <Text style={styles.modalLabel}>Status</Text>
                  <StatusBadge status={selectedPayment.status} dueDate={selectedPayment.due_date} />
                </View>

                <View style={styles.divider} />

                <Text style={styles.timelineTitle}>Timeline Pembayaran</Text>
                <TimelineStep
                  label="Tagihan dibuat"
                  sub={`Jatuh tempo ${formatShortDate(selectedPayment.due_date)}`}
                  done
                />
                <TimelineStep
                  label="Notifikasi WhatsApp dikirim"
                  sub="Pengingat otomatis"
                  done
                />
                <TimelineStep
                  label="Pembayaran lunas"
                  sub={
                    selectedPayment.payment_date
                      ? formatShortDate(selectedPayment.payment_date)
                      : 'Menunggu konfirmasi...'
                  }
                  done={selectedPayment.status === 'paid'}
                />

                {selectedPayment.proof_image ? (
                  <TouchableOpacity style={styles.proofBtn}>
                    <Text style={styles.proofBtnText}>Lihat Bukti Transfer</Text>
                  </TouchableOpacity>
                ) : null}

                {selectedPayment.status === 'pending' && (
                  <View style={styles.uploadSection}>
                    <View style={styles.divider} />
                    <Text style={[styles.uploadTitle, { color: COLORS.indigo }]}>
                      Bukti pembayaran telah dikirim
                    </Text>
                    <Text style={{ color: COLORS.textSecondary, fontSize: FONT_SIZE.sm }}>
                      Menunggu verifikasi dari admin. Status akan diperbarui setelah dikonfirmasi.
                    </Text>
                  </View>
                )}

                {(selectedPayment.status === 'unpaid' || selectedPayment.status === 'overdue') && (
                  <View style={styles.uploadSection}>
                    <View style={styles.divider} />
                    <Text style={styles.uploadTitle}>Upload Bukti Pembayaran</Text>

                    <TouchableOpacity style={styles.pickFileBtn} onPress={handlePickProof}>
                      <Text style={styles.pickFileBtnText}>
                        {proofFile ? `✓ ${proofFile.name}` : '+ Pilih Foto / File'}
                      </Text>
                    </TouchableOpacity>

                    <TouchableOpacity
                      style={[styles.submitBtn, (isSubmitting || !proofFile) && styles.submitBtnDisabled]}
                      onPress={handleSubmitPayment}
                      disabled={isSubmitting || !proofFile}
                    >
                      {isSubmitting
                        ? <ActivityIndicator color="#fff" size="small" />
                        : <Text style={styles.submitBtnText}>Kirim Pembayaran</Text>
                      }
                    </TouchableOpacity>
                  </View>
                )}

                <TouchableOpacity style={styles.closeBtn} onPress={closeModal}>
                  <Text style={styles.closeBtnText}>Tutup</Text>
                </TouchableOpacity>
              </ScrollView>
            </GlassCard>
          )}
        </View>
      </Modal>

      <BottomNavBar
        activeTab="Payment"
        onTabPress={(key) => {
          closeModal()
          navigation.navigate(key as any)
        }}
        unreadCount={unreadCount}
      />
    </View>
  )
}

function ModalRow({ label, value, highlight }: { label: string; value: string; highlight?: boolean }) {
  return (
    <View style={styles.modalRow}>
      <Text style={styles.modalLabel}>{label}</Text>
      <Text style={[styles.modalValue, highlight && { color: COLORS.accent, fontWeight: '700' }]}>
        {value}
      </Text>
    </View>
  )
}

function TimelineStep({ label, sub, done }: { label: string; sub: string; done: boolean }) {
  return (
    <View style={styles.timelineItem}>
      <View style={[styles.timelineDot, done && styles.timelineDotDone]} />
      <View style={styles.timelineText}>
        <Text style={[styles.timelineLabel, done && { color: COLORS.textPrimary }]}>{label}</Text>
        <Text style={styles.timelineSub}>{sub}</Text>
      </View>
    </View>
  )
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: COLORS.background },
  safe:      { flex: 1 },

  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
  },
  backBtn:  { padding: 4, marginRight: SPACING.sm },
  backIcon: { color: COLORS.textPrimary, fontSize: 24 },
  title:    { flex: 1, color: COLORS.textPrimary, fontSize: FONT_SIZE.xl, fontWeight: '700' },

  filterScroll: { maxHeight: 50 },
  filterRow: {
    paddingHorizontal: SPACING.lg,
    paddingBottom: SPACING.sm,
    gap: SPACING.sm,
  },
  tab: {
    paddingHorizontal: SPACING.md,
    paddingVertical: 7,
    borderRadius: RADIUS.full,
    borderWidth: 0.5,
    borderColor: COLORS.surfaceBorder,
    backgroundColor: 'rgba(255,255,255,0.04)',
  },
  tabActive: {
    backgroundColor: COLORS.accentMuted,
    borderColor: COLORS.accent + '55',
  },
  tabText:       { color: COLORS.textSecondary, fontSize: FONT_SIZE.sm, fontWeight: '500' },
  tabTextActive: { color: COLORS.accent, fontWeight: '700' },

  listContent: {
    paddingHorizontal: SPACING.lg,
    paddingTop: SPACING.sm,
    paddingBottom: 120,
  },
  emptyCard:  { padding: SPACING.xl, alignItems: 'center' },
  emptyText:  { color: COLORS.textHint, fontSize: FONT_SIZE.md },

  // Modal
  modalOverlay: {
    flex: 1,
    justifyContent: 'flex-end',
    backgroundColor: 'rgba(0,0,0,0.55)',
  },
  sheet: {
    margin: SPACING.md,
    marginBottom: 90,
    padding: SPACING.lg,
    maxHeight: '82%',
  },
  sheetHandle: {
    width: 40,
    height: 4,
    borderRadius: 2,
    backgroundColor: COLORS.textHint,
    alignSelf: 'center',
    marginBottom: SPACING.lg,
  },
  sheetTitle: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.xl,
    fontWeight: '700',
    marginBottom: 4,
  },
  sheetMonth: {
    color: COLORS.accent,
    fontSize: FONT_SIZE.lg,
    fontWeight: '600',
    marginBottom: SPACING.sm,
  },
  divider: {
    height: 0.5,
    backgroundColor: COLORS.surfaceBorder,
    marginVertical: SPACING.md,
  },
  modalRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: SPACING.sm,
  },
  statusModalRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  modalLabel: { color: COLORS.textSecondary, fontSize: FONT_SIZE.md },
  modalValue: { color: COLORS.textPrimary,   fontSize: FONT_SIZE.md, fontWeight: '500' },

  timelineTitle: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.lg,
    fontWeight: '700',
    marginBottom: SPACING.md,
  },
  timelineItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: SPACING.md,
  },
  timelineDot: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: COLORS.textHint,
    marginTop: 3,
    marginRight: SPACING.md,
  },
  timelineDotDone: { backgroundColor: COLORS.success },
  timelineText:    { flex: 1 },
  timelineLabel: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.md,
    fontWeight: '500',
  },
  timelineSub: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.sm,
    marginTop: 2,
  },

  proofBtn: {
    backgroundColor: COLORS.accentMuted,
    borderRadius: RADIUS.md,
    padding: SPACING.md,
    alignItems: 'center',
    marginTop: SPACING.md,
    borderWidth: 0.5,
    borderColor: COLORS.accent + '44',
  },
  proofBtnText: { color: COLORS.accent, fontWeight: '700', fontSize: FONT_SIZE.md },

  uploadSection: { marginTop: SPACING.sm },
  uploadTitle: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.lg,
    fontWeight: '700',
    marginBottom: SPACING.md,
  },
  pickFileBtn: {
    borderWidth: 1,
    borderColor: COLORS.accent + '66',
    borderStyle: 'dashed',
    borderRadius: RADIUS.md,
    padding: SPACING.md,
    alignItems: 'center',
    marginBottom: SPACING.md,
    backgroundColor: COLORS.accentMuted,
  },
  pickFileBtnText: {
    color: COLORS.accent,
    fontSize: FONT_SIZE.md,
    fontWeight: '600',
  },
  submitBtn: {
    backgroundColor: COLORS.accent,
    borderRadius: RADIUS.md,
    padding: SPACING.md,
    alignItems: 'center',
    marginBottom: SPACING.sm,
  },
  submitBtnDisabled: { opacity: 0.45 },
  submitBtnText: { color: '#fff', fontWeight: '700', fontSize: FONT_SIZE.md },

  closeBtn: {
    backgroundColor: 'rgba(255,255,255,0.06)',
    borderRadius: RADIUS.md,
    padding: SPACING.md,
    alignItems: 'center',
    marginTop: SPACING.sm,
    marginBottom: SPACING.sm,
  },
  closeBtnText: { color: COLORS.textSecondary, fontWeight: '600', fontSize: FONT_SIZE.md },
})
