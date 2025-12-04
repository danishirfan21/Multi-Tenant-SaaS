import { describe, it, expect, vi, beforeEach } from 'vitest'
import { render, screen } from '@testing-library/react'
import { BrowserRouter } from 'react-router-dom'
import Dashboard from '../pages/Dashboard'
import { AuthProvider } from '../contexts/AuthContext'

// Mock useAuth hook
vi.mock('../contexts/AuthContext', async () => {
  const actual = await vi.importActual('../contexts/AuthContext')
  return {
    ...actual,
    useAuth: () => ({
      user: {
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        role: 'admin',
        tenant_id: 1,
        is_active: true,
        created_at: '2024-01-01',
        updated_at: '2024-01-01',
      },
      isAuthenticated: true,
      isLoading: false,
      login: vi.fn(),
      logout: vi.fn(),
      updateUser: vi.fn(),
    }),
  }
})

describe('Dashboard Page', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('renders dashboard heading', () => {
    render(
      <BrowserRouter>
        <AuthProvider>
          <Dashboard />
        </AuthProvider>
      </BrowserRouter>
    )

    expect(screen.getByText(/dashboard/i)).toBeInTheDocument()
    expect(screen.getByText(/welcome back, test user!/i)).toBeInTheDocument()
  })
})
