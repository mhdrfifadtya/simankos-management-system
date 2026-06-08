import axios, { AxiosInstance } from 'axios'
import AsyncStorage from '@react-native-async-storage/async-storage'
import Constants from 'expo-constants'

const getDevHost = (): string => {
  // hostUri dari Expo berbentuk "192.168.x.x:8081", ambil bagian IP-nya
  const hostUri = Constants.expoConfig?.hostUri
  if (hostUri) {
    return hostUri.split(':')[0]
  }
  return 'localhost'
}

const BASE_URL = __DEV__
  ? `http://${getDevHost()}:8000/api/v1`
  : 'https://api.kosapp.com/api/v1'

const api: AxiosInstance = axios.create({
  baseURL: BASE_URL,
  timeout: 15000,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === 401) {
      await AsyncStorage.removeItem('auth_token')
    }
    return Promise.reject(error)
  }
)

export default api
