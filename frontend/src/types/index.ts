export interface User {
  id: number
  tenant_id: number
  name: string
  email: string
  role: 'owner' | 'admin' | 'user'
  is_active: boolean
  created_at: string
  updated_at: string
  tenant?: Tenant
}

export interface Tenant {
  id: number
  name: string
  slug: string
  domain?: string
  settings?: Record<string, any>
  is_active: boolean
  created_at: string
  updated_at: string
}

export interface Project {
  id: number
  tenant_id: number
  user_id: number
  name: string
  description?: string
  status: 'active' | 'on_hold' | 'completed' | 'archived'
  start_date?: string
  end_date?: string
  created_at: string
  updated_at: string
  tasks_count?: number
  user?: User
  tasks?: Task[]
}

export interface Task {
  id: number
  tenant_id: number
  project_id: number
  user_id?: number
  title: string
  description?: string
  status: 'todo' | 'in_progress' | 'done'
  priority: 'low' | 'medium' | 'high' | 'urgent'
  due_date?: string
  order: number
  is_overdue: boolean
  created_at: string
  updated_at: string
  project?: Project
  user?: User
}

export interface AuthResponse {
  access_token: string
  token_type: string
  expires_in: number
  user: User
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: {
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
