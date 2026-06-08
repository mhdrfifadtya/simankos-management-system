import React, { useMemo } from 'react'
import { View, StyleSheet, Dimensions } from 'react-native'
import Svg, {
  Rect,
  Circle,
  Defs,
  LinearGradient,
  Stop,
} from 'react-native-svg'

const { width: W, height: H } = Dimensions.get('window')

interface Building {
  x: number
  w: number
  h: number
  windows: { x: number; y: number; w: number; h: number }[]
}

const BUILDINGS: Building[] = [
  { x: 0,   w: 38,  h: 110, windows: [{ x: 8, y: 18, w: 8, h: 8 }, { x: 22, y: 18, w: 8, h: 8 }, { x: 8, y: 34, w: 8, h: 8 }] },
  { x: 33,  w: 28,  h: 80,  windows: [{ x: 5, y: 14, w: 6, h: 6 }, { x: 17, y: 14, w: 6, h: 6 }] },
  { x: 56,  w: 52,  h: 155, windows: [
    { x: 8, y: 18, w: 10, h: 10 }, { x: 28, y: 18, w: 10, h: 10 },
    { x: 8, y: 38, w: 10, h: 10 }, { x: 28, y: 38, w: 10, h: 10 },
    { x: 8, y: 58, w: 10, h: 10 }, { x: 28, y: 58, w: 10, h: 10 },
    { x: 8, y: 78, w: 10, h: 10 },
  ]},
  { x: 103, w: 24,  h: 65,  windows: [{ x: 5, y: 10, w: 5, h: 5 }, { x: 14, y: 10, w: 5, h: 5 }] },
  { x: 122, w: 44,  h: 195, windows: [
    { x: 8, y: 18, w: 8, h: 8 }, { x: 26, y: 18, w: 8, h: 8 },
    { x: 8, y: 36, w: 8, h: 8 }, { x: 26, y: 36, w: 8, h: 8 },
    { x: 8, y: 54, w: 8, h: 8 }, { x: 26, y: 54, w: 8, h: 8 },
    { x: 8, y: 72, w: 8, h: 8 }, { x: 26, y: 72, w: 8, h: 8 },
    { x: 8, y: 90, w: 8, h: 8 },
  ]},
  { x: 161, w: 34,  h: 105, windows: [
    { x: 7, y: 16, w: 7, h: 7 }, { x: 20, y: 16, w: 7, h: 7 },
    { x: 7, y: 32, w: 7, h: 7 }, { x: 20, y: 32, w: 7, h: 7 },
    { x: 7, y: 48, w: 7, h: 7 },
  ]},
  { x: 190, w: 58,  h: 138, windows: [
    { x: 10, y: 18, w: 10, h: 10 }, { x: 30, y: 18, w: 10, h: 10 },
    { x: 10, y: 38, w: 10, h: 10 }, { x: 30, y: 38, w: 10, h: 10 },
    { x: 10, y: 58, w: 10, h: 10 }, { x: 30, y: 58, w: 10, h: 10 },
  ]},
  { x: 243, w: 28,  h: 75,  windows: [{ x: 6, y: 12, w: 6, h: 6 }, { x: 16, y: 12, w: 6, h: 6 }] },
  { x: 266, w: 48,  h: 168, windows: [
    { x: 8, y: 18, w: 9, h: 9 }, { x: 26, y: 18, w: 9, h: 9 },
    { x: 8, y: 36, w: 9, h: 9 }, { x: 26, y: 36, w: 9, h: 9 },
    { x: 8, y: 54, w: 9, h: 9 }, { x: 26, y: 54, w: 9, h: 9 },
    { x: 8, y: 72, w: 9, h: 9 }, { x: 26, y: 72, w: 9, h: 9 },
  ]},
  { x: 309, w: 32,  h: 90,  windows: [
    { x: 6, y: 14, w: 7, h: 7 }, { x: 19, y: 14, w: 7, h: 7 },
    { x: 6, y: 30, w: 7, h: 7 },
  ]},
  { x: 336, w: 42,  h: 128, windows: [
    { x: 8, y: 16, w: 8, h: 8 }, { x: 26, y: 16, w: 8, h: 8 },
    { x: 8, y: 34, w: 8, h: 8 }, { x: 26, y: 34, w: 8, h: 8 },
    { x: 8, y: 52, w: 8, h: 8 },
  ]},
  { x: 373, w: 22,  h: 58,  windows: [{ x: 4, y: 10, w: 5, h: 5 }, { x: 13, y: 10, w: 5, h: 5 }] },
  { x: 390, w: 50,  h: 142, windows: [
    { x: 8, y: 18, w: 9, h: 9 }, { x: 28, y: 18, w: 9, h: 9 },
    { x: 8, y: 36, w: 9, h: 9 }, { x: 28, y: 36, w: 9, h: 9 },
    { x: 8, y: 54, w: 9, h: 9 }, { x: 28, y: 54, w: 9, h: 9 },
  ]},
  { x: 435, w: 30,  h: 85,  windows: [
    { x: 6, y: 14, w: 6, h: 6 }, { x: 18, y: 14, w: 6, h: 6 },
    { x: 6, y: 28, w: 6, h: 6 },
  ]},
  { x: 460, w: 45,  h: 118, windows: [
    { x: 8, y: 16, w: 8, h: 8 }, { x: 28, y: 16, w: 8, h: 8 },
    { x: 8, y: 32, w: 8, h: 8 }, { x: 28, y: 32, w: 8, h: 8 },
    { x: 8, y: 48, w: 8, h: 8 },
  ]},
]

const WIN_COLORS = ['#7dd3fc', '#fbbf24', '#818cf8', '#93c5fd', '#c4b5fd', '#6ee7b7']

function seededRandom(seed: number): number {
  const x = Math.sin(seed + 1) * 10000
  return x - Math.floor(x)
}

export default function SkylineBackground() {
  const stars = useMemo(() =>
    Array.from({ length: 55 }, (_, i) => ({
      cx: seededRandom(i * 7.31) * W,
      cy: seededRandom(i * 3.73) * H * 0.42,
      r:  seededRandom(i * 11.17) * 1.5 + 0.5,
      opacity: seededRandom(i * 5.93) * 0.5 + 0.25,
    })), [])

  return (
    <View style={StyleSheet.absoluteFill} pointerEvents="none">
      <Svg width={W} height={H}>
        <Defs>
          <LinearGradient id="bgGrad" x1="0" y1="0" x2="0" y2="1">
            <Stop offset="0" stopColor="#0a0e1a" stopOpacity="1" />
            <Stop offset="0.6" stopColor="#0c1220" stopOpacity="1" />
            <Stop offset="1" stopColor="#0d1829" stopOpacity="1" />
          </LinearGradient>
          <LinearGradient id="groundGlow" x1="0" y1="0" x2="0" y2="1">
            <Stop offset="0" stopColor="#7dd3fc" stopOpacity="0.08" />
            <Stop offset="1" stopColor="#7dd3fc" stopOpacity="0" />
          </LinearGradient>
        </Defs>

        {/* Sky gradient */}
        <Rect x="0" y="0" width={W} height={H} fill="url(#bgGrad)" />

        {/* Stars */}
        {stars.map((s, i) => (
          <Circle key={`s${i}`} cx={s.cx} cy={s.cy} r={s.r} fill="#ffffff" fillOpacity={s.opacity} />
        ))}

        {/* Ground glow line */}
        <Rect x="0" y={H - 30} width={W} height={30} fill="url(#groundGlow)" />

        {/* Buildings — back layer (slightly dimmer) */}
        {BUILDINGS.map((b, bi) => {
          const by = H - b.h
          return (
            <React.Fragment key={`b${bi}`}>
              <Rect
                x={b.x}
                y={by}
                width={b.w}
                height={b.h}
                fill="#0a1020"
                stroke="rgba(125,211,252,0.08)"
                strokeWidth="0.5"
              />
              {b.windows.map((w, wi) => {
                const seed = bi * 137 + wi * 19
                const lit = seededRandom(seed) > 0.3
                if (!lit) return null
                const color = WIN_COLORS[(bi * 3 + wi) % WIN_COLORS.length]
                const opacity = seededRandom(seed + 7) * 0.35 + 0.35
                return (
                  <Rect
                    key={`w${bi}${wi}`}
                    x={b.x + w.x}
                    y={by + w.y}
                    width={w.w}
                    height={w.h}
                    fill={color}
                    fillOpacity={opacity}
                    rx={1}
                  />
                )
              })}
            </React.Fragment>
          )
        })}

        {/* Horizon glow */}
        <Rect x="0" y={H - 4} width={W} height={4} fill="rgba(125,211,252,0.12)" />
      </Svg>
    </View>
  )
}
