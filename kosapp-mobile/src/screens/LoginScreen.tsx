import React, { useState, useRef } from 'react'
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Alert,
  ScrollView,
  type TextInputProps,
} from 'react-native'
import { StatusBar } from 'expo-status-bar'
import { LinearGradient } from 'expo-linear-gradient'
import { SafeAreaView } from 'react-native-safe-area-context'
import Svg, { Path, Circle, Defs, RadialGradient, Stop } from 'react-native-svg'
import { COLORS, FONT_SIZE, SPACING, RADIUS } from '../constants/theme'
import SkylineBackground from '../components/SkylineBackground'
import { useAuthStore } from '../store/authStore'

// ─── Logo SVG ────────────────────────────────────────────────────────────────
function LogoBadge({ size = 72 }: { size?: number }) {
  return (
    <Svg width={size} height={size} viewBox="0 0 72 72">
      <Defs>
        <RadialGradient id="glow" cx="50%" cy="50%" r="50%">
          <Stop offset="0%" stopColor="#7dd3fc" stopOpacity="0.35" />
          <Stop offset="100%" stopColor="#7dd3fc" stopOpacity="0" />
        </RadialGradient>
      </Defs>
      {/* Outer glow */}
      <Circle cx="36" cy="36" r="36" fill="url(#glow)" />
      {/* Badge ring */}
      <Circle cx="36" cy="36" r="30" fill="rgba(15,23,42,0.92)"
        stroke="#7dd3fc" strokeWidth="0.75" strokeOpacity="0.5" />
      {/* Building icon */}
      <Path
        d="M20 50 L20 28 L28 22 L36 28 L36 22 L44 16 L52 22 L52 50 Z"
        fill="none"
        stroke="#7dd3fc"
        strokeWidth="1.5"
        strokeLinejoin="round"
        strokeLinecap="round"
      />
      <Path
        d="M28 50 L28 40 L36 40 L36 50"
        fill="rgba(125,211,252,0.18)"
        stroke="#7dd3fc"
        strokeWidth="1.2"
        strokeLinejoin="round"
      />
      <Path
        d="M40 34 L44 34 L44 38 L40 38 Z M46 34 L50 34 L50 38 L46 38 Z M40 42 L44 42 L44 46 L40 46 Z M46 42 L50 42 L50 46 L46 46 Z"
        fill="#7dd3fc"
        fillOpacity="0.6"
      />
    </Svg>
  )
}

// ─── Input field dengan focus state ──────────────────────────────────────────
interface FieldProps extends TextInputProps {
  label: string
  rightElement?: React.ReactNode
}

const InputField = React.forwardRef<TextInput, FieldProps>(
  ({ label, rightElement, ...props }, ref) => {
    const [focused, setFocused] = useState(false)
    return (
      <View style={field.wrapper}>
        <Text style={field.label}>{label}</Text>
        <View style={[field.row, focused && field.rowFocused]}>
          <TextInput
            ref={ref}
            style={field.input}
            placeholderTextColor={COLORS.textHint}
            onFocus={() => setFocused(true)}
            onBlur={() => setFocused(false)}
            {...props}
          />
          {rightElement}
        </View>
      </View>
    )
  },
)

const field = StyleSheet.create({
  wrapper: { marginBottom: SPACING.md },
  label: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.xs,
    fontWeight: '600',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
    marginBottom: 8,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.04)',
    borderRadius: RADIUS.md,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.08)',
    paddingHorizontal: SPACING.md,
    height: 52,
  },
  rowFocused: {
    borderColor: COLORS.accent,
    backgroundColor: 'rgba(125,211,252,0.05)',
  },
  input: {
    flex: 1,
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.md,
    paddingVertical: 0,
  },
})

// ─── Screen utama ─────────────────────────────────────────────────────────────
export default function LoginScreen() {
  const [email, setEmail]       = useState('')
  const [password, setPassword] = useState('')
  const [showPw, setShowPw]     = useState(false)
  const { login, isLoading }    = useAuthStore()
  const passwordRef             = useRef<TextInput>(null)

  const handleLogin = async () => {
    if (!email.trim() || !password.trim()) {
      Alert.alert('Peringatan', 'Email dan kata sandi wajib diisi.')
      return
    }
    try {
      await login(email.trim().toLowerCase(), password)
    } catch {
      Alert.alert(
        'Login Gagal',
        'Email atau kata sandi tidak valid.\n\nDemo: penyewa@kos.com / password',
      )
    }
  }

  return (
    <View style={styles.root}>
      <StatusBar style="light" />
      <SkylineBackground />

      {/* Gradient overlay lebih dramatis di bawah */}
      <LinearGradient
        colors={['rgba(10,14,26,0.1)', 'rgba(10,14,26,0.6)', 'rgba(10,14,26,0.97)']}
        locations={[0, 0.45, 1]}
        style={StyleSheet.absoluteFill}
      />

      <SafeAreaView style={styles.safe}>
        <KeyboardAvoidingView
          behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
          style={styles.kav}
        >
          <ScrollView
            contentContainerStyle={styles.scroll}
            keyboardShouldPersistTaps="handled"
            showsVerticalScrollIndicator={false}
          >
            {/* ── Branding ── */}
            <View style={styles.brand}>
              <LogoBadge size={80} />

              <View style={styles.brandText}>
                <Text style={styles.appName}>
                  <Text style={{ color: COLORS.accent }}>Kos</Text>
                  <Text style={{ color: COLORS.white }}>App</Text>
                </Text>
                <Text style={styles.tagline}>Sistem Manajemen Kos Digital</Text>
              </View>

              {/* Divider dekoratif */}
              <View style={styles.brandDivider}>
                <View style={styles.divLine} />
                <View style={styles.divDot} />
                <View style={styles.divLine} />
              </View>
            </View>

            {/* ── Form card ── */}
            <View style={styles.card}>
              <View style={styles.cardHeader}>
                <Text style={styles.cardTitle}>Masuk ke Akun</Text>
                <Text style={styles.cardSubtitle}>
                  Masukkan kredensial Anda untuk melanjutkan
                </Text>
              </View>

              <View style={styles.cardBody}>
                <InputField
                  label="Alamat Email"
                  placeholder="email@contoh.com"
                  value={email}
                  onChangeText={setEmail}
                  autoCapitalize="none"
                  keyboardType="email-address"
                  returnKeyType="next"
                  onSubmitEditing={() => passwordRef.current?.focus()}
                  editable={!isLoading}
                />

                <InputField
                  label="Kata Sandi"
                  placeholder="Masukkan kata sandi"
                  value={password}
                  onChangeText={setPassword}
                  secureTextEntry={!showPw}
                  returnKeyType="done"
                  onSubmitEditing={handleLogin}
                  editable={!isLoading}
                  ref={passwordRef}
                  rightElement={
                    <TouchableOpacity
                      onPress={() => setShowPw(v => !v)}
                      style={styles.eyeBtn}
                      hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
                    >
                      <Text style={styles.eyeText}>{showPw ? '○' : '◎'}</Text>
                    </TouchableOpacity>
                  }
                />

                {/* Login button */}
                <TouchableOpacity
                  onPress={handleLogin}
                  disabled={isLoading}
                  activeOpacity={0.88}
                  style={styles.btnWrap}
                >
                  <LinearGradient
                    colors={isLoading
                      ? ['rgba(125,211,252,0.35)', 'rgba(129,140,248,0.35)']
                      : ['#7dd3fc', '#818cf8']}
                    start={{ x: 0, y: 0 }}
                    end={{ x: 1, y: 0 }}
                    style={styles.btn}
                  >
                    {isLoading ? (
                      <ActivityIndicator color="#fff" size="small" />
                    ) : (
                      <Text style={styles.btnText}>Masuk</Text>
                    )}
                  </LinearGradient>
                </TouchableOpacity>
              </View>

              {/* Footer card */}
              <View style={styles.cardFooter}>
                <View style={styles.footerDivider} />
                <Text style={styles.demoLabel}>Akun Demo</Text>
                <Text style={styles.demoValue}>penyewa@kos.com  ·  password</Text>
              </View>
            </View>

            {/* Legal text */}
            <Text style={styles.legal}>
              © 2026 SimAnKos — Sistem Manajemen Kos
            </Text>
          </ScrollView>
        </KeyboardAvoidingView>
      </SafeAreaView>
    </View>
  )
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: COLORS.background },
  safe: { flex: 1 },
  kav:  { flex: 1 },
  scroll: {
    flexGrow: 1,
    justifyContent: 'flex-end',
    paddingHorizontal: SPACING.lg,
    paddingBottom: SPACING.xl,
    paddingTop: SPACING.xxl,
  },

  // Branding
  brand: {
    alignItems: 'center',
    marginBottom: SPACING.xxl,
  },
  brandText: {
    alignItems: 'center',
    marginTop: SPACING.md,
  },
  appName: {
    fontSize: 36,
    fontWeight: '800',
    letterSpacing: 1.5,
    lineHeight: 42,
  },
  tagline: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.sm,
    letterSpacing: 0.4,
    marginTop: 4,
  },
  brandDivider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: SPACING.lg,
    width: 120,
  },
  divLine: {
    flex: 1,
    height: 0.5,
    backgroundColor: 'rgba(125,211,252,0.25)',
  },
  divDot: {
    width: 4,
    height: 4,
    borderRadius: 2,
    backgroundColor: COLORS.accent,
    opacity: 0.6,
    marginHorizontal: 6,
  },

  // Card
  card: {
    borderRadius: RADIUS.xl,
    borderWidth: 0.5,
    borderColor: 'rgba(255,255,255,0.09)',
    backgroundColor: 'rgba(12,18,36,0.88)',
    overflow: 'hidden',
  },
  cardHeader: {
    paddingHorizontal: SPACING.xl,
    paddingTop: SPACING.xl,
    paddingBottom: SPACING.lg,
    borderBottomWidth: 0.5,
    borderBottomColor: 'rgba(255,255,255,0.06)',
  },
  cardTitle: {
    color: COLORS.textPrimary,
    fontSize: FONT_SIZE.xl,
    fontWeight: '700',
    letterSpacing: 0.2,
  },
  cardSubtitle: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.sm,
    marginTop: 4,
  },
  cardBody: {
    paddingHorizontal: SPACING.xl,
    paddingTop: SPACING.lg,
    paddingBottom: SPACING.md,
  },

  // Button
  btnWrap: {
    borderRadius: RADIUS.md,
    overflow: 'hidden',
    marginTop: SPACING.sm,
    shadowColor: COLORS.accent,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 12,
    elevation: 6,
  },
  btn: {
    height: 52,
    alignItems: 'center',
    justifyContent: 'center',
  },
  btnText: {
    color: '#0a0e1a',
    fontSize: FONT_SIZE.md,
    fontWeight: '700',
    letterSpacing: 0.6,
  },

  // Eye toggle
  eyeBtn: { padding: 2 },
  eyeText: { color: COLORS.textHint, fontSize: 16 },

  // Footer
  cardFooter: {
    paddingHorizontal: SPACING.xl,
    paddingVertical: SPACING.md,
    alignItems: 'center',
    backgroundColor: 'rgba(255,255,255,0.02)',
  },
  footerDivider: {
    width: '100%',
    height: 0.5,
    backgroundColor: 'rgba(255,255,255,0.06)',
    marginBottom: SPACING.md,
  },
  demoLabel: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
    fontWeight: '600',
    letterSpacing: 0.8,
    textTransform: 'uppercase',
    marginBottom: 4,
  },
  demoValue: {
    color: COLORS.textSecondary,
    fontSize: FONT_SIZE.sm,
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },

  // Legal
  legal: {
    color: COLORS.textHint,
    fontSize: FONT_SIZE.xs,
    textAlign: 'center',
    marginTop: SPACING.lg,
    opacity: 0.5,
    letterSpacing: 0.3,
  },
})
