import { statuses } from '@/enums/app_objects_types'

export const isInAdminReview = status => status === statuses.ocrPostProcessingReview || status === statuses.processOcrOutputFileReview

export const isPtImageUpload = status => [statuses.uploadImageRequested, statuses.uploadImageFailed, statuses.uploadImageSucceeded].includes(status)
