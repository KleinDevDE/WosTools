import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/puzzles',
    name: 'albums',
    component: () => import('../views/AlbumsView.vue'),
    meta: { title: 'Albums' },
  },
  {
    path: '/puzzles/albums/:albumId/puzzles',
    name: 'puzzles',
    component: () => import('../views/PuzzlesView.vue'),
    meta: { title: 'Puzzles' },
  },
  {
    path: '/puzzles/puzzles/:puzzleId/pieces',
    name: 'pieces',
    component: () => import('../views/PiecesView.vue'),
    meta: { title: 'Pieces' },
  },
  {
    path: '/puzzles/matches',
    name: 'matches',
    component: () => import('../views/MatchesView.vue'),
    meta: { title: 'Matches' },
  },
  {
    path: '/puzzles/search',
    name: 'search',
    component: () => import('../views/SearchView.vue'),
    meta: { title: 'Search' },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/puzzles',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0 };
  },
});

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - Puzzle Trading` : 'Puzzle Trading';
  next();
});

export default router;
