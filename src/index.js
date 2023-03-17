import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import './styles/App.css'; // Import global styles

/**
 * The main entry point for the React application.
 * This file sets up the root React component and renders it into the DOM.
 */

// Find the root DOM element where the React app will be mounted.
// It's expected to be an element with id 'root' in public/index.html.
const rootElement = document.getElementById('root');

// Ensure the root element exists before attempting to render.
if (rootElement) {
  // Create a React root for concurrent mode rendering.
  const root = ReactDOM.createRoot(rootElement);

  // Render the main App component into the root.
  // React.StrictMode is a tool for highlighting potential problems in an application.
  // It activates additional checks and warnings for its descendants during development mode.
  root.render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
} else {
  // Log an error if the root element is not found, which would prevent the app from starting.
  console.error('Failed to find the root element. Make sure an element with id "root" exists in your index.html.');
}