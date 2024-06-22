import './App.css'
import CategoryListPage from "./components/categories/list/CategoryListPage.tsx";
import React from "react";

const App:React.FC = () => {
  return (
      <div className="container mx-auto px-4 py-8">
          <CategoryListPage/>
      </div>
  )
}

export default App
