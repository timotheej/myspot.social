import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  categories: {},
  errors: {}
}

// getters
export const getters = {
  categories: state => state.categories
}

// mutations
export const mutations = {
  FETCH_ALL_CATEGORIES (state, { categories }) {
    state.categories = categories
  },
  SET_ERRORS (state, e) {
    state.errors = e
  }
}

// actions
export const actions = {
  async fetchAllCategories ({ commit }) {
    try {
      const { data } = await axios.get('/api/categories')
      commit('FETCH_ALL_CATEGORIES', { categories: data })
    } catch (e) {
      commit('SET_ERRORS', e)
    }
  }
}
