import Vue from 'vue'
import Child from './Child'
import Button from './Button'
import Checkbox from './Checkbox'
import Modal from './Modal'
import Overlay from './Overlay'
import ImageUpload from './ImageUpload'
import UploadProgressBar from './UploadProgressBar'
import ImageSlider from './ImageSlider'
import RatingSpot from './RatingSpot'
import { VueAgile } from 'vue-agile'
import { HasError, AlertError, AlertSuccess } from 'vform'

// Components that are registered globaly.
;[
  Child,
  Button,
  Checkbox,
  Modal,
  Overlay,
  ImageUpload,
  UploadProgressBar,
  ImageSlider,
  RatingSpot,
  VueAgile,
  HasError,
  AlertError,
  AlertSuccess
].forEach(Component => {
  Vue.component(Component.name, Component)
})
