import React, { useState, useEffect, useCallback } from 'react';
import TodoList from './components/TodoList';
import api from './services/api';
import './styles/App.css';

/**
 * Main application component for the Interactive Todo List.
 * Manages the state of todos, interacts with the backend API for CRUD operations,
 * and renders the TodoList component.
 */
function App() {
  // State to hold the list of todo items
  const [todos, setTodos] = useState([]);
  // State to hold the text for a new todo item
  const [newTodoText, setNewTodoText] = useState('');
  // State to manage loading status (optional, but good for UX)
  const [isLoading, setIsLoading] = useState(true);
  // State to manage potential errors
  const [error, setError] = useState(null);

  /**
   * Fetches all todo items from the backend API when the component mounts.
   * Uses `useEffect` with an empty dependency array to run only once.
   */
  useEffect(() => {
    const fetchTodos = async () => {
      try {
        setIsLoading(true);
        setError(null); // Clear previous errors
        const data = await api.getTodos();
        setTodos(data);
      } catch (err) {
        console.error('Failed to fetch todos:', err);
        setError('Failed to load todos. Please try again later.');
      } finally {
        setIsLoading(false);
      }
    };

    fetchTodos();
  }, []); // Empty dependency array ensures this runs only once on mount

  /**
   * Handles adding a new todo item.
   * Prevents default form submission, calls the API, and updates the state.
   * @param {Object} event - The form submission event.
   */
  const handleAddTodo = useCallback(async (event) => {
    event.preventDefault(); // Prevent page reload

    const trimmedText = newTodoText.trim();
    if (!trimmedText) {
      alert('Todo text cannot be empty.');
      return;
    }

    try {
      setError(null); // Clear previous errors
      const newTodo = await api.addTodo({ text: trimmedText });
      setTodos((prevTodos) => [...prevTodos, newTodo]); // Add new todo to the list
      setNewTodoText(''); // Clear the input field
    } catch (err) {
      console.error('Failed to add todo:', err);
      setError('Failed to add todo. Please try again.');
    }
  }, [newTodoText]); // Recreate if newTodoText changes

  /**
   * Handles toggling the completion status of a todo item.
   * Calls the API to update the todo and then updates the local state.
   * @param {number} id - The ID of the todo item to update.
   * @param {boolean} currentCompletedStatus - The current completion status of the todo.
   */
  const handleToggleComplete = useCallback(async (id, currentCompletedStatus) => {
    try {
      setError(null); // Clear previous errors
      const updatedTodo = await api.updateTodo(id, { completed: !currentCompletedStatus });
      setTodos((prevTodos) =>
        prevTodos.map((todo) =>
          todo.id === id ? { ...todo, completed: updatedTodo.completed } : todo
        )
      );
    } catch (err) {
      console.error(`Failed to toggle todo ${id}:`, err);
      setError('Failed to update todo status. Please try again.');
    }
  }, []); // No dependencies, as it operates on an ID and current status

  /**
   * Handles deleting a todo item.
   * Calls the API to delete the todo and then updates the local state.
   * @param {number} id - The ID of the todo item to delete.
   */
  const handleDeleteTodo = useCallback(async (id) => {
    try {
      setError(null); // Clear previous errors
      await api.deleteTodo(id);
      setTodos((prevTodos) => prevTodos.filter((todo) => todo.id !== id)); // Remove todo from the list
    } catch (err) {
      console.error(`Failed to delete todo ${id}:`, err);
      setError('Failed to delete todo. Please try again.');
    }
  }, []); // No dependencies, as it operates on an ID

  // Render the application UI
  return (
    <div className="App">
      <header className="App-header">
        <h1>Interactive Todo List</h1>
      </header>
      <main className="App-main">
        {/* Form for adding new todos */}
        <form onSubmit={handleAddTodo} className="todo-form">
          <input
            type="text"
            value={newTodoText}
            onChange={(e) => setNewTodoText(e.target.value)}
            placeholder="Add a new todo..."
            aria-label="New todo text"
            className="todo-input"
          />
          <button type="submit" className="add-button">Add Todo</button>
        </form>

        {/* Display loading, error, or the todo list */}
        {isLoading ? (
          <p className="status-message">Loading todos...</p>
        ) : error ? (
          <p className="error-message">{error}</p>
        ) : todos.length === 0 ? (
          <p className="status-message">No todos yet! Add one above.</p>
        ) : (
          <TodoList
            todos={todos}
            onToggleComplete={handleToggleComplete}
            onDeleteTodo={handleDeleteTodo}
          />
        )}
      </main>
    </div>
  );
}

export default App;