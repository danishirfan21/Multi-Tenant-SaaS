import React, { useEffect, useState } from 'react'
import { useParams, useNavigate, Link } from 'react-router-dom'
import Layout from '../components/layout/Layout'
import apiClient from '../api/client'
import LoadingSpinner from '../components/common/LoadingSpinner'
import Button from '../components/common/Button'
import { Project, Task } from '../types'

const ProjectDetail: React.FC = () => {
  const { id } = useParams<{ id: string }>()
  const navigate = useNavigate()
  const [project, setProject] = useState<Project | null>(null)
  const [tasks, setTasks] = useState<Task[]>([])
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    const fetchProject = async () => {
      try {
        const response = await apiClient.get<{ data: Project }>(`/projects/${id}`)
        setProject(response.data.data)
        setTasks(response.data.data.tasks || [])
      } catch (error) {
        console.error('Failed to fetch project:', error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchProject()
  }, [id])

  const getStatusColor = (status: string) => {
    const colors = {
      todo: 'bg-gray-100 text-gray-800',
      in_progress: 'bg-yellow-100 text-yellow-800',
      done: 'bg-green-100 text-green-800',
    }
    return colors[status as keyof typeof colors] || 'bg-gray-100 text-gray-800'
  }

  const tasksByStatus = {
    todo: tasks.filter(t => t.status === 'todo'),
    in_progress: tasks.filter(t => t.status === 'in_progress'),
    done: tasks.filter(t => t.status === 'done'),
  }

  if (isLoading) return <Layout><LoadingSpinner /></Layout>
  if (!project) return <Layout><p>Project not found</p></Layout>

  return (
    <Layout>
      <div className="space-y-6">
        <div className="flex justify-between items-start">
          <div>
            <button
              onClick={() => navigate('/projects')}
              className="text-blue-600 hover:text-blue-800 mb-2"
            >
              ← Back to Projects
            </button>
            <h1 className="text-3xl font-bold text-gray-900">{project.name}</h1>
            <p className="mt-2 text-gray-600">{project.description}</p>
          </div>
          <span className="px-3 py-1 text-sm font-medium rounded bg-green-100 text-green-800">
            {project.status}
          </span>
        </div>

        <div className="bg-white p-6 rounded-lg shadow">
          <h2 className="text-xl font-semibold mb-4">Tasks</h2>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {Object.entries(tasksByStatus).map(([status, statusTasks]) => (
              <div key={status} className="space-y-3">
                <h3 className="font-medium text-gray-900 capitalize">
                  {status.replace('_', ' ')} ({statusTasks.length})
                </h3>
                <div className="space-y-2">
                  {statusTasks.map((task) => (
                    <div key={task.id} className="bg-gray-50 p-4 rounded border">
                      <h4 className="font-medium text-gray-900 text-sm">{task.title}</h4>
                      <p className="text-xs text-gray-500 mt-1">{task.description}</p>
                      <div className="mt-2 flex items-center gap-2">
                        <span className={`px-2 py-1 text-xs font-medium rounded ${getStatusColor(task.status)}`}>
                          {task.status}
                        </span>
                        <span className="text-xs text-gray-500">{task.priority}</span>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </Layout>
  )
}

export default ProjectDetail
