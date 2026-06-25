<svg style="width: 0; height: 0; position: absolute;" aria-hidden="true">
  <filter id="clear-glass-filter" x="-20%" y="-20%" width="140%" height="140%" color-interpolation-filters="sRGB">
    <feTurbulence type="fractalNoise" baseFrequency="0.005" numOctaves="1" result="noise" />
    <feGaussianBlur in="noise" stdDeviation="4" result="smoothedNoise" />
    <feDisplacementMap in="SourceGraphic" in2="smoothedNoise" scale="35" xChannelSelector="R" yChannelSelector="G" />
  </filter>
</svg>