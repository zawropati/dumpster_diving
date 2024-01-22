import axios from 'axios';

var baseApiUrl = "http://localhost:8080/dumpster-diving/backend/"

export const HTTP = axios.create({
  baseURL: baseApiUrl
})
