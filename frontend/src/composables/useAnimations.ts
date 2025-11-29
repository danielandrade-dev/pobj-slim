import { useMotion } from '@vueuse/motion'
import { onMounted, ref } from 'vue'

/**
 * Composable para animações suaves usando @vueuse/motion
 */
export function useAnimations() {
  /**
   * Animação de fade in com slide up
   */
  const fadeInUp = {
    initial: { opacity: 0, y: 20 },
    enter: { opacity: 1, y: 0 },
    transition: { duration: 0.4, ease: [0.25, 0.1, 0.25, 1] }
  }

  /**
   * Animação de fade in com slide down
   */
  const fadeInDown = {
    initial: { opacity: 0, y: -20 },
    enter: { opacity: 1, y: 0 },
    transition: { duration: 0.4, ease: [0.25, 0.1, 0.25, 1] }
  }

  /**
   * Animação de fade in
   */
  const fadeIn = {
    initial: { opacity: 0 },
    enter: { opacity: 1 },
    transition: { duration: 0.3, ease: 'easeOut' }
  }

  /**
   * Animação de scale in
   */
  const scaleIn = {
    initial: { opacity: 0, scale: 0.95 },
    enter: { opacity: 1, scale: 1 },
    transition: { duration: 0.3, ease: [0.25, 0.1, 0.25, 1] }
  }

  /**
   * Animação de slide in da direita
   */
  const slideInRight = {
    initial: { opacity: 0, x: 20 },
    enter: { opacity: 1, x: 0 },
    transition: { duration: 0.3, ease: [0.25, 0.1, 0.25, 1] }
  }

  /**
   * Animação de slide in da esquerda
   */
  const slideInLeft = {
    initial: { opacity: 0, x: -20 },
    enter: { opacity: 1, x: 0 },
    transition: { duration: 0.3, ease: [0.25, 0.1, 0.25, 1] }
  }

  /**
   * Animação de hover suave
   */
  const hoverScale = {
    hover: { scale: 1.05 },
    tap: { scale: 0.98 }
  }

  /**
   * Animação de stagger para listas
   */
  const staggerChildren = (delay: number = 0.05) => ({
    transition: {
      staggerChildren: delay
    }
  })

  return {
    fadeInUp,
    fadeInDown,
    fadeIn,
    scaleIn,
    slideInRight,
    slideInLeft,
    hoverScale,
    staggerChildren
  }
}

/**
 * Hook para animação de entrada com delay
 */
export function useStaggeredAnimation(delay: number = 0) {
  const isVisible = ref(false)

  onMounted(() => {
    setTimeout(() => {
      isVisible.value = true
    }, delay)
  })

  return {
    isVisible,
    animationProps: {
      initial: { opacity: 0, y: 20 },
      enter: { opacity: 1, y: 0 },
      transition: { duration: 0.4, delay, ease: [0.25, 0.1, 0.25, 1] }
    }
  }
}

