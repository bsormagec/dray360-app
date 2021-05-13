import { statuses, displayStatuses } from '@/enums/app_objects_types'

export const isInAdminReview = status => [statuses.ocrPostProcessingReview, statuses.processOcrOutputFileReview].includes(status)

export const isInProcessing = displayStatus => displayStatuses.processing === displayStatus

export const isPtImageUpload = status => [statuses.uploadImageRequested, statuses.uploadImageFailed, statuses.uploadImageSucceeded].includes(status)
