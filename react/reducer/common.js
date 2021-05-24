import {
    SET_MESSAGE,
    HIDE_MESSAGE,
    SET_TOAST_MESSAGE,
    GET_COMPANIES_LOGOS,
    GET_SPAM_LIST,
    GET_USERS_LIST
} from "../constants";

const initialState = {
    toast: false,
    alertMessage: false,
    logos: [],
    spamFilters: false,
    users: [],
};

const commonReducer = (state = initialState, action) => {
    switch (action.type) {
        case GET_SPAM_LIST:
            return {...state, spamFilters: action.payload};
        case SET_TOAST_MESSAGE:
            return {...state, toast: action.payload};
        case GET_COMPANIES_LOGOS:
            return {...state, logos: action.payload};
        case SET_MESSAGE:
            return {...state, alertMessage: action.payload};
        case HIDE_MESSAGE:
            return {...state, alertMessage: false};
        case GET_USERS_LIST:
            return {...state, users: action.payload};
        default:
            return  state;
    }
}

export default commonReducer;
