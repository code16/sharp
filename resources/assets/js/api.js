import axios from 'axios';
import { API_PATH } from "./consts";
import paramsSerializer from './helpers/paramsSerializer';

export const api = axios.create({
    baseURL: API_PATH,
    paramsSerializer,
});