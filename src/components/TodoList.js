import React from 'react';
import PropTypes from 'prop-types';

/**
 * TodoList Component
 *
 * Displays a list of todo items, allowing users to mark them as complete
 * or delete them.
 *
 * @param {object} props - The component props.
 * @param {Array<object>} props.todos - An array of todo objects, each with `id`, `text`, and `completed` properties.
 * @param {function(number): void} props.onToggleComplete - Callback function to toggle the completion status of a todo.
 * @param {function(number): void} props.onDelete - Callback function to delete a todo.
 * @returns {JSX.Element} The TodoList component.
 */
const TodoList = ({ todos, onToggleComplete, onDelete }) => {
  if (!todos || todos.length === 0) {
    return <p className="no-todos-message">No tasks yet! Add a new one above.</p>;
  }

  return (
    <ul className="todo-list">
      {todos.map((todo) => (
        <li
          key={todo.id}
          className={`todo-item ${todo.completed ? 'completed' : ''}`}
        >
          <div className="todo-item-content">
            <input
              type="checkbox"
              checked={todo.completed}
              onChange={() => onToggleComplete(todo.id)}
              className="todo-checkbox"
              aria-label={`Mark "${todo.text}" as ${todo.completed ? 'incomplete' : 'complete'}`}
            />
            <span className="todo-text" onClick={() => onToggleComplete(todo.id)}>
              {todo.text}
            </span>
          </div>
          <button
            onClick={() => onDelete(todo.id)}
            className="delete-button"
            aria-label={`Delete "${todo.text}"`}
          >
            &times;
          </button>
        </li>
      ))}
    </ul>
  );
};

TodoList.propTypes = {
  todos: PropTypes.arrayOf(
    PropTypes.shape({
      id: PropTypes.number.isRequired,
      text: PropTypes.string.isRequired,
      completed: PropTypes.bool.isRequired,
    })
  ).isRequired,
  onToggleComplete: PropTypes.func.isRequired,
  onDelete: PropTypes.func.isRequired,
};

export default TodoList;