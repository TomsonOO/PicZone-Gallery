import React, { useState } from 'react';


const Navbar = () => {
    return (
        <nav className="bg-gray-800 text-white p-4">
            <div className="container mx-auto flex justify-between items-center">
                <h1 className="text-lg font-bold">Web Gallery</h1>
            </div>
        </nav>
    );
};

export default Navbar;
