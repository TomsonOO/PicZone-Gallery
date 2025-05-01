export class GetBookInfoQuery {
  constructor(
    public readonly title: string,
    public readonly author: string,
    public readonly query: string,
  ) {}
}
