import React from 'react'
import { NavigationContainer } from '@react-navigation/native'
import { createStackNavigator } from '@react-navigation/stack'
import { useAuthStore } from '../store/authStore'
import LoginScreen from '../screens/LoginScreen'
import HomeScreen from '../screens/HomeScreen'
import PaymentScreen from '../screens/PaymentScreen'
import NotificationScreen from '../screens/NotificationScreen'
import ProfileScreen from '../screens/ProfileScreen'

export type RootStackParamList = {
  Login: undefined
  Home: undefined
  Payment: undefined
  Notification: undefined
  Profile: undefined
}

const Stack = createStackNavigator<RootStackParamList>()

export default function AppNavigator() {
  const { token } = useAuthStore()

  return (
    <NavigationContainer>
      <Stack.Navigator
        screenOptions={{
          headerShown: false,
          contentStyle: { backgroundColor: '#0a0e1a' },
        }}
      >
        {!token ? (
          <Stack.Screen name="Login" component={LoginScreen} />
        ) : (
          <>
            <Stack.Screen name="Home"         component={HomeScreen} />
            <Stack.Screen name="Payment"      component={PaymentScreen} />
            <Stack.Screen name="Notification" component={NotificationScreen} />
            <Stack.Screen name="Profile"      component={ProfileScreen} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  )
}
