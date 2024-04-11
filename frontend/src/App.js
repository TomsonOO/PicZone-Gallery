import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import HomePage from './pages/HomePage';

function App() {
    return (
        <Router>
        <div className="flex flex-col bg-green-100 shadow-lg min-h-screen">
                <Navbar className=""/>
                <main className="flex-grow">
                    <div className="container mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <Routes>
                            <Route path="/" element={<HomePage />} />
                        </Routes>
                    </div>
                </main>
                <Footer />
            </div>
        </Router>
    );
}

export default App;
