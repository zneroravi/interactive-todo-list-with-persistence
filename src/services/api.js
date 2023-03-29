/**
 * @file src/services/api.js
 * @description API service for interacting with the backend Todo list.
 *              Handles all HTTP requests to the PHP API.
 */

// Determine the API base URL.
// It prioritizes the environment variable REACT_APP_API_BASE_URL,
// falling back to a common development URL if not set.
const API_BASE_URL = process.env.REACT_APP_API_BASE_URL || 'http://localhost:8000/backend';

/**
 * Generic helper function to handle API responses.
 * Checks for HTTP errors and parses JSON.
 * @param {Response} response - The fetch API response object.
 * @returns {Promise<Object>} - The parsed JSON data.
 * @throws {Error} - Throws an error if the response status is not OK.
 */
const handleResponse = async (response) => {
  if (!response.ok) {
    const errorData = await response.json().catch(() => ({ message: 'Unknown error' }));
    const errorMessage = errorData.message || `HTTP error! Status: ${response.status}`;
    throw new Error(errorMessage);
  }
  return response.json();
};

/**
 * Fetches all todos from the backend.
 * @returns {Promise<Array<Object>>} - A promise that resolves to an array of todo objects.
 * @throws {Error} - Throws an error if the API call fails.
 */
export const getTodos = async () => {
  try {
    const response = await fetch(`${API_BASE_URL}/index.php`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    return await handleResponse(response);
  } catch (error) {
    console.error('Error fetching todos:', error);
    throw error; // Re-throw to allow component to handle
  }
};

/**
 * Adds a new todo to the backend.
 * @param {string} text - The text content of the new todo.
 * @returns {Promise<Object>} - A promise that resolves to the newly created todo object.
 * @throws {Error} - Throws an error if the API call fails.
 */
export const addTodo = async (text) => {
  try {
    const response = await fetch(`${API_BASE_URL}/index.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ text }),
    });
    return await handleResponse(response);
  } catch (error) {
    console.error('Error adding todo:', error);
    throw error;
  }
};

/**
 * Updates an existing todo's completion status in the backend.
 * @param {number} id - The ID of the todo to update.
 * @param {boolean} completed - The new completion status.
 * @returns {Promise<Object>} - A promise that resolves to the updated todo object.
 * @throws {Error} - Throws an error if the API call fails.
 */
export const updateTodo = async (id, completed) => {
  try {
    const response = await fetch(`${API_BASE_URL}/index.php`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id, completed }),
    });
    return await handleResponse(response);
  } catch (error) {
    console.error(`Error updating todo with ID ${id}:`, error);
    throw error;
  }
};

/**
 * Deletes a todo from the backend.
 * @param {number} id - The ID of the todo to delete.
 * @returns {Promise<Object>} - A promise that resolves to a success message or confirmation.
 * @throws {Error} - Throws an error if the API call fails.
 */
export const deleteTodo = async (id) => {
  try {
    const response = await fetch(`${API_BASE_URL}/index.php`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id }),
    });
    return await handleResponse(response);
  } catch (error) {
    console.error(`Error deleting todo with ID ${id}:`, error);
    throw error;
  }
};

// Export the base URL for potential debugging or other uses if needed
export { API_BASE_URL };