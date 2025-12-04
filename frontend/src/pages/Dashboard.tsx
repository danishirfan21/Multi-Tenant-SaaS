import React, { useEffect, useState } from 'react'
import { useAuth } from '../contexts/AuthContext'
import Layout from '../components/layout/Layout'
import apiClient from '../api/client'
import LoadingSpinner from '../components/common/LoadingSpinner'

interface Stats {
  total: number
  active: number
  completed: number
  on_hold: number
}

const Dashboard: React.FC = () => {
  const { user } = useAuth()
  const [stats, setStats] = useState<Stats | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await apiClient.get<{ data: Stats }>('/projects/stats')
        setStats(response.data.data)
      } catch (error) {
        console.error('Failed to fetch stats:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchStats()
  }, [])

  return (
    <Layout>
      <div className="space-y-6">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
          <p className="mt-2 text-gray-600">
            Welcome back, {user?.name}! Here's your project overview.
          </p>
        </div>

        {isLoading ? (
          <LoadingSpinner />
        ) : stats ? (
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="bg-white p-6 rounded-lg shadow">
              <h3 className="text-sm font-medium text-gray-500">Total Projects</h3>
              <p className="mt-2 text-3xl font-bold text-gray-900">{stats.total || 0}</p>
            </div>

            <div className="bg-white p-6 rounded-lg shadow">
              <h3 className="text-sm font-medium text-gray-500">Active</h3>
              <p className="mt-2 text-3xl font-bold text-green-600">{stats.active || 0}</p>
            </div>

            <div className="bg-white p-6 rounded-lg shadow">
              <h3 className="text-sm font-medium text-gray-500">Completed</h3>
              <p className="mt-2 text-3xl font-bold text-blue-600">{stats.completed || 0}</p>
            </div>

            <div className="bg-white p-6 rounded-lg shadow">
              <h3 className="text-sm font-medium text-gray-500">On Hold</h3>
              <p className="mt-2 text-3xl font-bold text-yellow-600">{stats.on_hold || 0}</p>
            </div>
          </div>
        ) : (
          <p className="text-gray-500">No data available</p>
        )}

        <div className="bg-white p-6 rounded-lg shadow">
          <h2 className="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a
              href="/projects"
              className="block p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition"
            >
              <h3 className="font-medium text-gray-900">View Projects</h3>
              <p className="text-sm text-gray-500 mt-1">Manage all your projects</p>
            </a>

            <a
              href="/tasks"
              className="block p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition"
            >
              <h3 className="font-medium text-gray-900">View Tasks</h3>
              <p className="text-sm text-gray-500 mt-1">Track your task progress</p>
            </a>

            {(user?.role === 'owner' || user?.role === 'admin') && (
              <a
                href="/users"
                className="block p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition"
              >
                <h3 className="font-medium text-gray-900">Manage Users</h3>
                <p className="text-sm text-gray-500 mt-1">Add or edit team members</p>
              </a>
            )}
          </div>
        </div>
      </div>
    </Layout>
  )
}

export default Dashboard
