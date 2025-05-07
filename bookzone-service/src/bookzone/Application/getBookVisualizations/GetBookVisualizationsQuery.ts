import { VisualizationType } from 'src/bookzone/Domain/VisualizationType';

export class GetBookVisualizationsQuery {
  constructor(
    public readonly bookId: string,
    public readonly type?: VisualizationType
  ) {}
}
