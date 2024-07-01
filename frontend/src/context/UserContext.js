import React, { createContext, useReducer, useContext } from 'react';

const initialState = {
    user: null,
    token: null,
};

const reducer = (state, action) => {
    switch (action.type) {
        case 'LOGIN':
            return {
                ...state,
                user: action.payload.user,
                token: action.payload.token,
            };
        case 'LOGOUT':
            return {
                ...state,
                user: null,
                token: null,
            };
        default:
            return state;
    }
};

const UserContext = createContext();

export const UserProvider = ({ children }) => {
    const [state, dispatch] = useReducer(reducer, initialState);

    const login = (user, token) => {
        dispatch({ type: 'LOGIN', payload: { user, token } });
        localStorage.setItem('token', token);
    };

    const logout = () => {
        dispatch({ type: 'LOGOUT' });
        localStorage.removeItem('token');
    };

    return (
        <UserContext.Provider value={{ state, login, logout }}>
            {children}
        </UserContext.Provider>
    );
};

export const useUser = () => useContext(UserContext);

export default UserContext;
