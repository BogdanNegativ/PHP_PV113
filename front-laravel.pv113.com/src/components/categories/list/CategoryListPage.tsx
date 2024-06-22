import CategoryItem from "./CategoryItem.tsx";
import axios from 'axios';
import React, {useEffect, useState} from "react";
import {Category} from "../../../interfaces/interfaces.ts";

const CategoryListPage:React.FC = () =>{
    const [categories, setCategories] = useState<Category[]>([]);

    useEffect(() => {
        axios.get<Category[]>('http://laravel.pv113.com/api/categories')
            .then(response => {
                setCategories(response.data);
            })
            .catch(error => {
                console.error('Error fetching categories:', error);
            });
    }, []);

    return(
        <div className="container mx-auto px-4 py-8">
            <h1 className="text-3xl font-bold text-center mb-8">Category List</h1>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {categories.map(category => (
                    <CategoryItem key={category.id} {...category} />
                ))}
            </div>
        </div>
    );
}
export default CategoryListPage;